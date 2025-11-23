<?php

namespace App\Apis\OshiKatsuSaport;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Repositories\MstTalentRepository;

class OshiKatsuSaportController extends Controller
{
    private $mstTalentRepository;
    public function __construct() {
        $this->mstTalentRepository = new MstTalentRepository();
    }

    /**
     * タレント一覧取得API
     * GET /oshi-katsu-saport/talents
     *
     * @return JsonResponse
     */
    public function talents(): JsonResponse
    {
        $mstTalents = $this->mstTalentRepository->all();
        $responseData = [
            'status' => true,
            'data' => [
                'talents' => $mstTalents->map(function ($mstTalent) {
                    return [
                        'id' => $mstTalent->id,
                        'talentName' => $mstTalent->talent_name,
                        'talentNameEn' => $mstTalent->talent_name_en,
                    ];
                }),
            ],
        ];
        return response()->json($responseData);
    }

    /**
     * タレント別ハッシュタグ取得API
     * GET /oshi-katsu-saport/talents/{id}/hashtags
     *
     * @param string $id
     * @return JsonResponse
     */
    public function talentHashtags(string $id): JsonResponse
    {
        $talentHashtagsData = $this->getTalentHashtagsData();

        if (!isset($talentHashtagsData[$id])) {
            return response()->json([
                'status' => false,
                'message' => 'Talent not found',
            ], 404);
        }

        $talentData = $talentHashtagsData[$id];

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
            '1' => [
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
                        'description' => '通常配信',
                    ],
                    [
                        'id' => 3,
                        'tag' => 'ときのそらFC',
                        'description' => 'ファンクラブ限定配信',
                    ],
                    [
                        'id' => 4,
                        'tag' => 'ときのそらスペース',
                        'description' => 'Twitterスペース',
                    ],
                    [
                        'id' => 5,
                        'tag' => 'soraArt',
                        'description' => 'ファンアート',
                    ],
                    [
                        'id' => 6,
                        'tag' => 'そらArt',
                        'description' => 'ファンアート',
                    ],
                    [
                        'id' => 7,
                        'tag' => 'ときのそら聞いたよ',
                        'description' => 'ボイス感想',
                    ],
                    [
                        'id' => 8,
                        'tag' => 'ときのそらと一緒',
                        'description' => 'ぬいぐるみ・推し活写真投稿用',
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
            '2' => [
                'talent' => [
                    'id' => '2',
                    'key' => 'roboco',
                    'name' => 'ロボ子さん',
                ],
                'hashtags' => [
                    [
                        'id' => 1,
                        'tag' => 'ロボ子さん',
                        'description' => '公式',
                    ],
                    [
                        'id' => 2,
                        'tag' => 'ロボ子生放送',
                        'description' => '通常配信',
                    ],
                    [
                        'id' => 3,
                        'tag' => '秘密のロボ子さん',
                        'description' => 'メンバー限定配信',
                    ],
                    [
                        'id' => 4,
                        'tag' => 'RBCSPACE',
                        'description' => 'Twitterスペース',
                    ],
                    [
                        'id' => 5,
                        'tag' => 'ロボ子Art',
                        'description' => 'ファンアート',
                    ],
                    [
                        'id' => 6,
                        'tag' => '聴いたよロボ子さん',
                        'description' => 'ボイス感想',
                    ],
                    [
                        'id' => 7,
                        'tag' => 'みてみてろぼろぼ',
                        'description' => 'ぬいぐるみ・推し活写真投稿用',
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
                        'tag' => 'RBNAIL',
                        'description' => 'ろぼさー作サムネ',
                    ],
                ],
                'eventHashtags' => [],
            ],
        ];
    }
}
