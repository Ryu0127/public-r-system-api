<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * 許可されたドメインからのアクセスのみを許可するミドルウェア
 * 特定のAPIエンドポイントに対してドメイン制限を適用する
 */
class CheckAllowedOrigin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 環境変数から許可するドメインを取得
        $allowedOriginsEnv = env('ALLOWED_ORIGINS');

        // 許可ドメインが設定されていない場合は、全てのドメインを許可
        if (empty($allowedOriginsEnv)) {
            return $next($request);
        }

        // カンマ区切りで複数ドメインに対応
        $allowedOrigins = array_map('trim', explode(',', $allowedOriginsEnv));

        // リクエストのOriginヘッダーを取得
        $origin = $request->header('Origin');

        // Originヘッダーがない場合（ブラウザ以外からのリクエストなど）は許可
        if (empty($origin)) {
            return $next($request);
        }

        // 末尾のスラッシュを削除して比較
        $origin = rtrim($origin, '/');
        $allowedOrigins = array_map(function ($domain) {
            return rtrim($domain, '/');
        }, $allowedOrigins);

        // 許可されたドメインからのアクセスかチェック
        if (!in_array($origin, $allowedOrigins)) {
            return response()->json([
                'status' => false,
                'message' => 'このドメインからのアクセスは許可されていません',
            ], 403);
        }

        return $next($request);
    }
}
