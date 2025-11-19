<?php

namespace App\Http\Middleware;

use App\Repositories\TblAuthTokenRepository;
use Closure;
use Illuminate\Http\Request;

/**
 * トークン認証ミドルウェア
 * Authorizationヘッダーから Bearer トークンを抽出して認証
 */
class TokenAuthenticate
{
    private $tblAuthTokenRepository;

    public function __construct()
    {
        $this->tblAuthTokenRepository = new TblAuthTokenRepository();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Authorizationヘッダーを取得
        $authorizationHeader = $request->header('Authorization');

        // Authorizationヘッダーが存在しない場合
        if (!$authorizationHeader) {
            return response()->json([
                'status' => false,
                'message' => '認証トークンが提供されていません',
            ], 401);
        }

        // Bearer トークンの形式チェック
        if (!preg_match('/^Bearer\s+(.+)$/i', $authorizationHeader, $matches)) {
            return response()->json([
                'status' => false,
                'message' => '認証トークンの形式が正しくありません',
            ], 401);
        }

        $token = $matches[1];

        // トークンをデータベースで検索
        $authToken = $this->tblAuthTokenRepository->findByToken($token);

        // トークンが見つからない場合
        if (!$authToken) {
            return response()->json([
                'status' => false,
                'message' => '無効な認証トークンです',
            ], 401);
        }

        // 有効期限チェック
        if (now()->greaterThan($authToken->expiration_date)) {
            return response()->json([
                'status' => false,
                'message' => '認証トークンの有効期限が切れています',
            ], 401);
        }

        // リクエストにユーザーIDを追加（コントローラーで使用可能）
        $request->merge(['authenticated_user_id' => $authToken->user_id]);

        return $next($request);
    }
}
