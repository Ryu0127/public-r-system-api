<?php

namespace App\Apis\Events;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class EventsController extends Controller
{
    /**
     * イベント詳細モックデータ
     *
     * @var array
     */
    private static $mockEventsData = [
        [
            'id' => '1',
            'title' => 'hololive production OFFICIAL POP UP SHOP',
            'date' => '2026-01-09',
            'endDate' => '2026-01-22',
            'startTime' => '10:00',
            'endTime' => '20:30',
            'type' => 'goods',
            'talentNames' => ['ホロライブプロダクション', 'さくらみこ', '星街すいせい'],
            'description' => '東京駅一番街 いちばんプラザにてポップアップストアを開催。0期生、hololive Indonesia 1期生、hololive English -Myth-メンバーの「感謝」の花束をモチーフにした描き下ろしイラストグッズを販売。

【営業時間】10:00 〜 20:30
※1月22日は18:00までの営業となります。

【来場予約について】
一部日時は事前抽選による予約制となります。
■事前抽選実施対象日時
・1月9日（金）10:00～12:00
・1月10日（土）10:00～12:00
・1月11日（日）10:00～12:00
・1月16日（金）10:00～12:00
・1月17日（土）10:00～12:00
・1月18日（日）10:00～12:00

【購入者ノベルティ】
※特典はなくなり次第終了となります。
クリアカード・ポストカードは、開催期間の前半/後半で絵柄が異なります！
・前半：1月9日（金）〜1月15日（木）
・後半：1月16日（金）〜1月22日（木）

① オリジナルショッパー（全1種）
  11,000円（税込）以上のお買い上げで1点プレゼント

② メッセージカード（全3種/ランダム）
  1会計ごとに1点プレゼント

③ ポストカード（全11種/ランダム/上限11枚）
  2,200円（税込）ごとに1点プレゼント

④ クリアカード（全11種/ランダム/上限11枚）
  5,500円（税込）ごとに1点プレゼント',
            'color' => '#1E90FF',
            'url' => 'https://hololive.hololivepro.com/events/popup-2601/',
            'thumbnailUrl' => 'https://placehold.co/800x400/1E90FF/FFFFFF?text=POP+UP+SHOP',
            'location' => '東京駅一番街内「いちばんプラザ」',
            'notes' => [
                '※1月22日は18:00までの営業となります。',
                '※一部日時は事前抽選による予約制となります。',
            ],
            'status' => 'draft',
            'createdAt' => '2025-12-01T10:00:00.000Z',
            'updatedAt' => '2025-12-10T15:30:00.000Z',
        ],
    ];

    /**
     * イベント一覧取得API
     * GET /events
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $responseData = [
            'success' => true,
            'data' => self::$mockEventsData,
            'message' => 'イベント一覧を取得しました',
        ];

        return response()->json($responseData);
    }

    /**
     * イベント詳細取得API
     * GET /events/{id}
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        // IDに該当するイベントを検索
        $event = collect(self::$mockEventsData)->firstWhere('id', $id);

        if (!$event) {
            $responseData = [
                'success' => false,
                'data' => null,
                'error' => "ID: {$id} のイベントが見つかりませんでした",
            ];

            return response()->json($responseData, 404);
        }

        $responseData = [
            'success' => true,
            'data' => $event,
            'message' => 'イベント詳細を取得しました',
        ];

        return response()->json($responseData);
    }
}
