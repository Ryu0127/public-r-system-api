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
                        'id' => 'feature-1',
                        'title' => '日次タスク管理',
                        'description' => '毎日のタスクを効率的に管理できます',
                        'icon' => 'calendar',
                        'link' => '/life/schedule-day',
                        'color' => '#4A90E2',
                    ],
                    [
                        'id' => 'feature-2',
                        'title' => '月次タスク管理',
                        'description' => '月単位でタスクを確認・管理できます',
                        'icon' => 'calendar-month',
                        'link' => '/life/schedule-month',
                        'color' => '#50C878',
                    ],
                    [
                        'id' => 'feature-3',
                        'title' => '通知設定',
                        'description' => 'タスクの通知を設定できます',
                        'icon' => 'bell',
                        'link' => '/settings/notifications',
                        'color' => '#F5A623',
                    ],
                    [
                        'id' => 'feature-4',
                        'title' => 'データ同期',
                        'description' => 'デバイス間でデータを同期できます',
                        'icon' => 'sync',
                        'link' => '/sync',
                        'color' => '#BD10E0',
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
                        'id' => 'log-1',
                        'version' => 'v2.1.0',
                        'date' => '2025-11-15',
                        'changes' => [
                            '通知機能の強化',
                            'UI/UXの改善',
                            'バグ修正',
                        ],
                    ],
                    [
                        'id' => 'log-2',
                        'version' => 'v2.0.0',
                        'date' => '2025-11-01',
                        'changes' => [
                            '月次タスク管理機能を追加',
                            'データ同期機能を追加',
                            'パフォーマンスの最適化',
                        ],
                    ],
                    [
                        'id' => 'log-3',
                        'version' => 'v1.5.0',
                        'date' => '2025-10-15',
                        'changes' => [
                            '日次タスク管理機能の改善',
                            '通知設定機能を追加',
                            'セキュリティの強化',
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
                    'id' => 'topic-1',
                    'title' => '新機能リリースキャンペーン',
                    'content' => '月次タスク管理機能がリリースされました！11月末までの期間限定で、プレミアム機能を無料でご利用いただけます。',
                    'startDate' => '2025-11-01T00:00:00',
                    'endDate' => '2025-11-30T23:59:59',
                ],
            ],
        ];

        return response()->json($responseData);
    }
}
