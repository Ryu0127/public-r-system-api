<?php

namespace App\Apis\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * ログアウトAPI（自動ログイントークンを無効化）
     * POST /auth/logout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // 自動ログイントークンが提供されている場合は無効化
        if ($request->has('autoLoginToken')) {
            $this->authService->revokeAutoLoginToken($request->input('autoLoginToken'));
        }

        return response()->json([
            'status' => true,
            'message' => 'ログアウトしました',
        ], 200);
    }
}
