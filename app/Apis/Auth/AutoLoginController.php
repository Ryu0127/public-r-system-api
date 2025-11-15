<?php

namespace App\Apis\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AutoLoginController extends Controller
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * 自動ログイン認証API
     * POST /auth/auto-login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'autoLoginToken' => 'required|string',
        ], [
            'autoLoginToken.required' => '自動ログイントークンは必須です',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'バリデーションエラー',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 自動ログイン処理
        $result = $this->authService->authenticateWithAutoLoginToken(
            $request->input('autoLoginToken')
        );

        // 認証失敗
        if (!$result['success']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
            ], 401);
        }

        // 認証成功 - 新しい認証トークンと自動ログイントークンを生成
        $authToken = Str::random(60); // Sanctumの代わりに簡易的なトークン生成

        // 古い自動ログイントークンを無効化
        $this->authService->revokeAutoLoginToken($request->input('autoLoginToken'));

        // 新しい自動ログイントークンを発行
        $newAutoLoginToken = $this->authService->generateAutoLoginToken($result['user']['id']);

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'data' => [
                'user' => $result['user'],
                'token' => $authToken,
                'autoLoginToken' => $newAutoLoginToken,
            ],
        ], 200);
    }
}
