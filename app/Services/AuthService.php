<?php

namespace App\Services;

use App\Repositories\MstUserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    // モックユーザーデータ
    // 実際のDB実装時には、UserモデルとAuthAttemptモデルに置き換えてください
    private static $mockUsers = [
        [
            'id' => 1,
            'email' => 'test@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'name' => 'Test User',
        ],
        [
            'id' => 2,
            'email' => 'user@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'name' => 'Sample User',
        ],
    ];

    // ログイン失敗回数とロック情報を管理（メモリ上に保存）
    // 実際のDB実装時には、tbl_login_attemptsテーブルに保存してください
    private static $loginAttempts = [];

    // 自動ログイントークンを管理（メモリ上に保存）
    // 実際のDB実装時には、tbl_auto_login_tokensテーブルに保存してください
    private static $autoLoginTokens = [];

    const MAX_LOGIN_ATTEMPTS_WARNING = 5;
    const MAX_LOGIN_ATTEMPTS_LOCK = 10;
    const LOCK_DURATION_MINUTES = 30;
    const AUTO_LOGIN_TOKEN_EXPIRY_DAYS = 30;

    /**
     * メールアドレスとパスワードでログイン認証
     *
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user' => array|null, 'warning' => bool, 'attempts' => int]
     */
    public function login(string $email, string $password, MstUserRepository $mstUserRepository): array
    {
        // ロック状態をチェック
        $lockCheck = $this->checkAccountLock($email);
        if (!$lockCheck['success']) {
            return $lockCheck;
        }

        // ユーザー検索
        $mstUser = $mstUserRepository->findByMailAddress($email);

        if (!$mstUser) {
            $this->incrementLoginAttempts($email);
            $attempts = $this->getLoginAttempts($email);

            return [
                'success' => false,
                'message' => 'メールアドレスまたはパスワードが正しくありません',
                'user' => null,
                'warning' => $attempts >= self::MAX_LOGIN_ATTEMPTS_WARNING,
                'attempts' => $attempts,
            ];
        }

        // パスワード検証
        if (!Hash::check($password, $mstUser['password'])) {
            $this->incrementLoginAttempts($email);
            $attempts = $this->getLoginAttempts($email);

            // 10回失敗でアカウントロック
            if ($attempts >= self::MAX_LOGIN_ATTEMPTS_LOCK) {
                $this->lockAccount($email);
                return [
                    'success' => false,
                    'message' => 'ログインに10回失敗したため、アカウントがロックされました。' . self::LOCK_DURATION_MINUTES . '分後に再試行してください。',
                    'user' => null,
                    'locked' => true,
                    'attempts' => $attempts,
                ];
            }

            return [
                'success' => false,
                'message' => 'メールアドレスまたはパスワードが正しくありません',
                'user' => null,
                'warning' => $attempts >= self::MAX_LOGIN_ATTEMPTS_WARNING,
                'attempts' => $attempts,
            ];
        }

        // ログイン成功 - 失敗回数をリセット
        $this->resetLoginAttempts($email);

        return [
            'success' => true,
            'message' => 'ログインに成功しました',
            'user' => [
                'id' => $mstUser['id'],
                'email' => $mstUser['mail_address'],
                'name' => $mstUser['user_name'],
            ],
            'warning' => false,
            'attempts' => 0,
        ];
    }

    /**
     * 自動ログイントークンを生成
     *
     * @param int $userId
     * @return string
     */
    public function generateAutoLoginToken(int $userId): string
    {
        $token = Str::random(64);
        $expiresAt = now()->addDays(self::AUTO_LOGIN_TOKEN_EXPIRY_DAYS);

        // トークンを保存（実際のDB実装時にはテーブルに保存）
        self::$autoLoginTokens[$token] = [
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ];

        return $token;
    }

    /**
     * 自動ログイントークンで認証
     *
     * @param string $autoLoginToken
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function authenticateWithAutoLoginToken(string $autoLoginToken): array
    {
        // トークン検索
        $tokenData = self::$autoLoginTokens[$autoLoginToken] ?? null;

        if (!$tokenData) {
            return [
                'success' => false,
                'message' => '無効な自動ログイントークンです',
                'user' => null,
            ];
        }

        // トークンの有効期限チェック
        if (now()->greaterThan($tokenData['expires_at'])) {
            // 期限切れトークンを削除
            unset(self::$autoLoginTokens[$autoLoginToken]);

            return [
                'success' => false,
                'message' => '自動ログイントークンの有効期限が切れています',
                'user' => null,
            ];
        }

        // ユーザー情報取得
        $user = $this->findUserById($tokenData['user_id']);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'ユーザーが見つかりません',
                'user' => null,
            ];
        }

        return [
            'success' => true,
            'message' => '自動ログインに成功しました',
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'],
            ],
        ];
    }

    /**
     * 自動ログイントークンを削除（ログアウト時など）
     *
     * @param string $autoLoginToken
     * @return void
     */
    public function revokeAutoLoginToken(string $autoLoginToken): void
    {
        unset(self::$autoLoginTokens[$autoLoginToken]);
    }

    /**
     * メールアドレスでユーザーを検索
     *
     * @param string $email
     * @return array|null
     */
    private function findUserByEmail(string $email): ?array
    {
        foreach (self::$mockUsers as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }

    /**
     * IDでユーザーを検索
     *
     * @param int $id
     * @return array|null
     */
    private function findUserById(int $id): ?array
    {
        foreach (self::$mockUsers as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }
        return null;
    }

    /**
     * ログイン失敗回数を取得
     *
     * @param string $email
     * @return int
     */
    private function getLoginAttempts(string $email): int
    {
        return self::$loginAttempts[$email]['count'] ?? 0;
    }

    /**
     * ログイン失敗回数をインクリメント
     *
     * @param string $email
     * @return void
     */
    private function incrementLoginAttempts(string $email): void
    {
        if (!isset(self::$loginAttempts[$email])) {
            self::$loginAttempts[$email] = [
                'count' => 0,
                'locked_until' => null,
            ];
        }
        self::$loginAttempts[$email]['count']++;
    }

    /**
     * ログイン失敗回数をリセット
     *
     * @param string $email
     * @return void
     */
    private function resetLoginAttempts(string $email): void
    {
        if (isset(self::$loginAttempts[$email])) {
            self::$loginAttempts[$email]['count'] = 0;
            self::$loginAttempts[$email]['locked_until'] = null;
        }
    }

    /**
     * アカウントロック状態をチェック
     *
     * @param string $email
     * @return array
     */
    private function checkAccountLock(string $email): array
    {
        if (!isset(self::$loginAttempts[$email]['locked_until'])) {
            return ['success' => true];
        }

        $lockedUntil = self::$loginAttempts[$email]['locked_until'];

        if ($lockedUntil && now()->lessThan($lockedUntil)) {
            $remainingMinutes = now()->diffInMinutes($lockedUntil) + 1;
            return [
                'success' => false,
                'message' => 'アカウントがロックされています。あと' . $remainingMinutes . '分後に再試行してください。',
                'locked' => true,
                'user' => null,
            ];
        }

        // ロック期間が過ぎていたらリセット
        if ($lockedUntil && now()->greaterThanOrEqualTo($lockedUntil)) {
            $this->resetLoginAttempts($email);
        }

        return ['success' => true];
    }

    /**
     * アカウントをロック
     *
     * @param string $email
     * @return void
     */
    private function lockAccount(string $email): void
    {
        if (!isset(self::$loginAttempts[$email])) {
            self::$loginAttempts[$email] = ['count' => 0];
        }
        self::$loginAttempts[$email]['locked_until'] = now()->addMinutes(self::LOCK_DURATION_MINUTES);
    }
}
