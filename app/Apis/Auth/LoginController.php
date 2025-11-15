<?php

namespace App\Apis\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * ログイン認証API
     * POST /auth/login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'メールアドレスは必須です',
            'email.email' => '有効なメールアドレスを入力してください',
            'password.required' => 'パスワードは必須です',
            'password.min' => 'パスワードは6文字以上で入力してください',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'バリデーションエラー',
                'errors' => $validator->errors(),
            ], 422);
        }

        // ログイン処理
        $result = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        // ログイン失敗
        if (!$result['success']) {
            $statusCode = isset($result['locked']) && $result['locked'] ? 423 : 401;

            $response = [
                'status' => false,
                'message' => $result['message'],
            ];

            // 警告フラグを追加
            if (isset($result['warning']) && $result['warning']) {
                $response['warning'] = true;
                $response['warningMessage'] = 'ログインに' . $result['attempts'] . '回失敗しています。10回失敗するとアカウントがロックされます。';
            }

            // ロック状態を追加
            if (isset($result['locked']) && $result['locked']) {
                $response['locked'] = true;
            }

            return response()->json($response, $statusCode);
        }

        // ログイン成功 - 認証トークンと自動ログイントークンを生成
        $authToken = Str::random(60); // Sanctumの代わりに簡易的なトークン生成
        $autoLoginToken = $this->authService->generateAutoLoginToken($result['user']['id']);

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'data' => [
                'user' => $result['user'],
                'token' => $authToken,
                'autoLoginToken' => $autoLoginToken,
            ],
        ], 200);
    }
}
