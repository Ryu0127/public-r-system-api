<?php

namespace App\Apis\OshiKatsuSaport;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class OshiKatsuSaportController extends Controller
{
    /**
     * タレント一覧取得API
     * GET /oshi-katsu-saport/talents
     *
     * @return JsonResponse
     */
    public function talents(): JsonResponse
    {
        $responseData = [
            'status' => true,
            'data' => [
                'talents' => [
                    [
                        'id' => '1',
                        'key' => 'tokinosora',
                        'name' => 'ときのそら',
                    ],
                    [
                        'id' => '2',
                        'key' => 'roboco',
                        'name' => 'ロボ子さん',
                    ],
                ],
            ],
        ];

        return response()->json($responseData);
    }

    /**
     * タレント別ハッシュタグ取得API
     * GET /oshi-katsu-saport/talents/{talentKey}/hashtags
     *
     * @param string $talentKey
     * @return JsonResponse
     */
    public function talentHashtags(string $talentKey): JsonResponse
    {
        $talentHashtagsData = $this->getTalentHashtagsData();

        if (!isset($talentHashtagsData[$talentKey])) {
            return response()->json([
                'status' => false,
                'message' => 'Talent not found',
            ], 404);
        }

        $talentData = $talentHashtagsData[$talentKey];

        $responseData = [
            'status' => true,
            'data' => [
                'talent' => $talentData['talent'],
                'hashtags' => $talentData['hashtags'],
                'eventHashtags' => $talentData['eventHashtags'],
            ],
        ];

        return response()->json($responseData);
    }

    /**
     * タレント別ハッシュタグモックデータ取得
     *
     * @return array
     */
    private function getTalentHashtagsData(): array
    {
        return [
            'tokinosora' => [
                'talent' => [
                    'id' => '1',
                    'key' => 'tokinosora',
                    'name' => 'ときのそら',
                ],
                'hashtags' => [
                    [
                        'id' => 1,
                        'tag' => 'ときのそら',
                        'description' => '一般',
                    ],
                    [
                        'id' => 2,
                        'tag' => 'ときのそら生放送',
                        'description' => '生放送',
                    ],
                    [
                        'id' => 3,
                        'tag' => 'soraArt',
                        'description' => 'ファンアート',
                    ],
                    [
                        'id' => 4,
                        'tag' => 'そらArt',
                        'description' => 'ファンアート',
                    ],
                    [
                        'id' => 5,
                        'tag' => 'ときのそら聞いたよ',
                        'description' => 'ボイス感想',
                    ],
                    [
                        'id' => 6,
                        'tag' => 'ときのそらFC',
                        'description' => 'ファンクラブ限定放送',
                    ],
                    [
                        'id' => 7,
                        'tag' => 'ときのそらと一緒',
                        'description' => 'ぬいぐるみ・ホロリー写真投稿用',
                    ],
                    [
                        'id' => 8,
                        'tag' => 'ときのそらスペース',
                        'description' => 'Twitterスペース',
                    ],
                    [
                        'id' => 9,
                        'tag' => 'ときのそらクラフト',
                        'description' => 'マインクラフト関係',
                    ],
                ],
                'eventHashtags' => [
                    [
                        'id' => 1,
                        'tag' => 'そらぱ2026',
                        'type' => 'live',
                        'eventName' => 'ときのそら　New Year\'s Party 2026「Dreams in Motion」',
                        'url' => 'https://tokinosora-live.com/sorapa2026/',
                    ],
                ],
            ],
            'roboco' => [
                'talent' => [
                    'id' => '2',
                    'key' => 'roboco',
                    'name' => 'ロボ子さん',
                ],
                'hashtags' => [
                    [
                        'id' => 1,
                        'tag' => 'robo_co',
                        'description' => '公式',
                    ],
                    [
                        'id' => 2,
                        'tag' => 'ロボ子Art',
                        'description' => 'ファンアート',
                    ],
                    [
                        'id' => 3,
                        'tag' => 'ロボ子生放送',
                        'description' => '生放送',
                    ],
                    [
                        'id' => 4,
                        'tag' => 'ろぼじゅーる',
                        'description' => 'スケジュール',
                    ],
                    [
                        'id' => 5,
                        'tag' => 'ろぼさー',
                        'description' => 'ファントーク',
                    ],
                    [
                        'id' => 6,
                        'tag' => '聴いたよロボ子さん',
                        'description' => 'ボイス感想',
                    ],
                    [
                        'id' => 7,
                        'tag' => 'RBCSPACE',
                        'description' => 'Twitterスペース',
                    ],
                    [
                        'id' => 8,
                        'tag' => 'ロボ子レクション',
                        'description' => '切り抜き',
                    ],
                    [
                        'id' => 9,
                        'tag' => 'カスタムロボ子さん',
                        'description' => '配信提供素材',
                    ],
                    [
                        'id' => 10,
                        'tag' => 'みてみてろぼろぼ',
                        'description' => 'ロボ子さんが見るタグ',
                    ],
                    [
                        'id' => 11,
                        'tag' => 'RBNAIL',
                        'description' => 'ろぼさー作サムネ',
                    ],
                ],
                'eventHashtags' => [],
            ],
        ];
    }
}
