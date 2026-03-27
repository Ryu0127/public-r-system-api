<?php

namespace App\Apis\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthSessionController extends Controller
{
    /**
     * Bearerトークンが有効か検証する（TokenAuthenticate 通過後のみ到達）
     * GET /auth/session
     */
    public function show(Request $request): JsonResponse
    {
        $userId = $request->input('authenticated_user_id');

        return response()->json([
            'status' => true,
            'message' => '認証トークンは有効です',
            'data' => [
                'userId' => $userId,
            ],
        ]);
    }
}
