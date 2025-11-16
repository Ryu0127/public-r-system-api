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
                        'id' => '1',
                        'version' => 'v2.5.0',
                        'date' => '2025-11-15',
                        'changes' => [
                            'Added AI-powered smart tag suggestions',
                            'Improved real-time trend analysis performance',
                            'Enhanced mobile responsive design',
                        ],
                    ],
                    [
                        'id' => '2',
                        'version' => 'v2.4.0',
                        'date' => '2025-10-28',
                        'changes' => [
                            'Added hashtag analytics dashboard',
                            'Implemented tag history tracking',
                            'Fixed minor UI bugs',
                        ],
                    ],
                    [
                        'id' => '3',
                        'version' => 'v2.3.0',
                        'date' => '2025-10-15',
                        'changes' => [
                            'Improved search performance',
                            'Added export functionality for tag data',
                            'Updated UI with Italian-inspired design',
                        ],
                    ],
                    [
                        'id' => '4',
                        'version' => 'v2.2.0',
                        'date' => '2025-09-30',
                        'changes' => [
                            'Added multi-language support',
                            'Enhanced tag recommendation algorithm',
                            'Improved accessibility features',
                        ],
                    ],
                    [
                        'id' => '5',
                        'version' => 'v2.1.0',
                        'date' => '2025-09-15',
                        'changes' => [
                            'Initial release of trending analysis',
                            'Added user preference settings',
                            'Performance optimizations',
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
                    'endDate' => '2025-12-31',
                ],
            ],
        ];

        return response()->json($responseData);
    }
}
