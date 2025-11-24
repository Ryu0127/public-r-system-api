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
        // 設定ファイルから許可するドメインを取得
        $allowedOriginsConfig = config('cors.allowed_origins_for_restriction');

        // 許可ドメインが設定されていない場合は、全てのドメインを許可
        if (empty($allowedOriginsConfig)) {
            return $next($request);
        }

        // カンマ区切りで複数ドメインに対応
        $allowedOrigins = array_map('trim', explode(',', $allowedOriginsConfig));

        // リクエストのOriginヘッダーを取得
        $origin = $request->header('Origin');

        // Originヘッダーがない場合はRefererヘッダーをチェック
        if (empty($origin)) {
            $referer = $request->header('Referer');

            if (empty($referer)) {
                // OriginもRefererもない場合は拒否
                return response()->json([
                    'status' => false,
                    'message' => 'このドメインからのアクセスは許可されていません',
                ], 403);
            }

            // RefererからオリジンURLを抽出
            $parsedUrl = parse_url($referer);
            if (!$parsedUrl || !isset($parsedUrl['scheme']) || !isset($parsedUrl['host'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'このドメインからのアクセスは許可されていません',
                ], 403);
            }

            $origin = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            if (isset($parsedUrl['port']) && !in_array($parsedUrl['port'], [80, 443])) {
                $origin .= ':' . $parsedUrl['port'];
            }
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
