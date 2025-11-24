<?php

namespace App\Apis\OshiKatsuSaport;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Repositories\MstTalentRepository;
use App\Repositories\MstTalentHashtagRepository;
use App\Repositories\MstEventTypeRepository;
use App\Repositories\TblEventCastTalentRepository;
use App\Repositories\TblEventRepository;
use App\Repositories\TblEventHashtagRepository;

class OshiKatsuSaportController extends Controller
{
    private $mstTalentRepository;
    private $mstTalentHashtagRepository;
    private $mstEventTypeRepository;
    private $tblEventCastTalentRepository;
    private $tblEventRepository;
    private $tblEventHashtagRepository;

    public function __construct() {
        $this->mstTalentRepository = new MstTalentRepository();
        $this->mstTalentHashtagRepository = new MstTalentHashtagRepository();
        $this->mstEventTypeRepository = new MstEventTypeRepository();
        $this->tblEventCastTalentRepository = new TblEventCastTalentRepository();
        $this->tblEventRepository = new TblEventRepository();
        $this->tblEventHashtagRepository = new TblEventHashtagRepository();
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
        $mstTalent = $this->mstTalentRepository->findPk($id);
        $mstTalentHashtags = $this->mstTalentHashtagRepository->getByTalentId($id);
        $mstEventTypes = $this->mstEventTypeRepository->all();
        $tblEventCastTalents = $this->tblEventCastTalentRepository->getByTalentId($id);
        $tblEvents = $this->tblEventRepository->getByPkIds($tblEventCastTalents->pluck('event_id')->toArray());
        $tblEventHashtags = $this->tblEventHashtagRepository->getByEventIds($tblEvents->pluck('id')->toArray());

        $responseData = [
            'status' => true,
            'data' => [
                'talent' => [
                    'id' => $mstTalent->id,
                    'name' => $mstTalent->talent_name,
                ],
                'hashtags' => $mstTalentHashtags->map(function ($mstTalentHashtag) {
                    return [
                        'id' => $mstTalentHashtag->id,
                        'tag' => $mstTalentHashtag->hashtag,
                        'description' => $mstTalentHashtag->description,
                    ];
                }),
                'eventHashtags' => $tblEvents->map(function ($tblEvent) use ($mstEventTypes, $tblEventHashtags) {
                    return [
                        'id' => $tblEvent->id,
                        'startDate' => $tblEvent->event_start_date,
                        'endDate' => $tblEvent->event_end_date,
                        'startTime' => $tblEvent->start_time,
                        'endTime' => $tblEvent->end_time,
                        'type' => $mstEventTypes->where('id', $tblEvent->event_type_id)->first()->event_type_name,
                        'eventName' => $tblEvent->event_name,
                        'url' => $tblEvent->event_url,
                        'tag' => $tblEventHashtags->where('event_id', $tblEvent->id)->pluck('hashtag')->implode(','),
                    ];
                }),
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
