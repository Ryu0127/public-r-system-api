<?php

namespace App\Apis\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * ホーム機能一覧取得API
     * GET /home/features
     *
     * @return JsonResponse
     */
    public function features(): JsonResponse
    {
        $responseData = [
            'status' => true,
            'data' => [
                'features' => [
                    [
                        'id' => 'talent-hashtag-support',
                        'title' => 'タレント別 ハッシュタグ投稿/検索 サポート',
                        'description' => '各タレントごとの応援ハッシュタグを選択して投稿/検索のサポートができます',
                        'icon' => 'Hash',
                        'link' => '/talent-hashtag-support',
                        'color' => 'amber',
                    ],
                    [
                        'id' => 'ego-search-support',
                        'title' => 'エゴサーチ サポート',
                        'description' => 'タレントのエゴサーチをサポートします。検索ワードやアカウント情報を確認できます',
                        'icon' => 'Search',
                        'link' => '/ego-search-support',
                        'color' => 'blue',
                    ],
                ],
            ],
        ];

        return response()->json($responseData);
    }

    /**
     * ホーム更新履歴取得API
     * GET /home/change-logs
     *
     * @return JsonResponse
     */
    public function changeLogs(): JsonResponse
    {
        $responseData = [
            'status' => true,
            'data' => [
                'changeLogs' => [
                    [
                        'id' => '2',
                        'version' => 'v1.1.0',
                        'date' => '2025-12-21',
                        'changes' => [
                            '「エゴサーチ サポート」機能を追加',
                        ],
                    ],
                    [
                        'id' => '1',
                        'version' => 'v1.0.0',
                        'date' => '2025-11-15',
                        'changes' => [
                            'サイトリリース',
                            '「タレント別 ハッシュタグ投稿/検索 サポート」機能を追加',
                        ],
                    ],
                ],
            ],
        ];

        return response()->json($responseData);
    }

    /**
     * ホーム期間限定トピック取得API
     * GET /home/limited-time-topic
     *
     * @return JsonResponse
     */
    public function limitedTimeTopic(): JsonResponse
    {
        $responseData = [
            'status' => true,
            'data' => [
                'limitedTimeTopic' => [
                    'id' => 'campaign-2025-winter',
                    'title' => 'ときのそら　New Year\'s Party 2026「Dreams in Motion」',
                    'content' => 'ときのそら　新年最初のライブイベント',
                    'startDate' => '2025-11-15',
                    'endDate' => '2025-11-16',
                ],
            ],
        ];

        return response()->json($responseData);
    }
}
