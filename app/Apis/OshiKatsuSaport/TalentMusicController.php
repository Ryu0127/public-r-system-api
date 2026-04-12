<?php

namespace App\Apis\OshiKatsuSaport;

use App\Http\Controllers\Controller;
use App\Repositories\MstTalentRepository;
use App\Repositories\MstYoutubeMusicVideoRepository;
use App\Repositories\RelYoutubeMusicVideoTalentRepository;
use App\Repositories\MstTalentGroupRepository;
use App\Repositories\RelTalentGroupMemberRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * タレント別楽曲一覧（フロント talent-music 画面用）
 * DB 未接続のためレスポンスは仮データ固定
 */
class TalentMusicController extends Controller
{
    private $mstTalentGroupRepository;
    private $mstTalentRepository;
    private $mstYoutubeMusicVideoRepository;
    private $relTalentGroupMemberRepository;
    private $relYoutubeMusicVideoTalentRepository;

    public function __construct(
        MstTalentGroupRepository $mstTalentGroupRepository,
        MstTalentRepository $mstTalentRepository,
        MstYoutubeMusicVideoRepository $mstYoutubeMusicVideoRepository,
        RelTalentGroupMemberRepository $relTalentGroupMemberRepository,
        RelYoutubeMusicVideoTalentRepository $relYoutubeMusicVideoTalentRepository,
    ){
        $this->mstTalentGroupRepository = $mstTalentGroupRepository;
        $this->mstTalentRepository = $mstTalentRepository;
        $this->mstYoutubeMusicVideoRepository = $mstYoutubeMusicVideoRepository;
        $this->relTalentGroupMemberRepository = $relTalentGroupMemberRepository;
        $this->relYoutubeMusicVideoTalentRepository = $relYoutubeMusicVideoTalentRepository;
    }

    /**
     * GET /oshi-katsu-saport/talent-music
     */
    public function index(Request $request): JsonResponse
    {
        $requests = [
            'group' => $request->query('group'),
            'talent' => $request->query('talent'),
        ];

        $mstTalentGroupAggregateList = $this->mstTalentGroupRepository->all();
        $mstTalentAggregateList = $this->mstTalentRepository->all();
        $mstYoutubeMusicVideoAggregateList = $this->mstYoutubeMusicVideoRepository->all();
        $relTalentGroupMemberAggregateList = $this->relTalentGroupMemberRepository->all();
        $relYoutubeMusicVideoTalentAggregateList = $this->relYoutubeMusicVideoTalentRepository->all();

        if (is_string($requests['group']) && trim($requests['group']) !== '') {
            $mstTalentGroupAggregateList = $mstTalentGroupAggregateList->filterByTalentGroupNameEnSlug(trim($requests['group']));
            $mstTalentGroupAggregate = $mstTalentGroupAggregateList->getAggregates()->first();
            $relTalentGroupMemberAggregateList = $relTalentGroupMemberAggregateList->filterByTalentGroup($mstTalentGroupAggregate);
            $mstTalentAggregateList = $mstTalentAggregateList->filterByTalentGroup($mstTalentGroupAggregate, $relTalentGroupMemberAggregateList);
        }
        if (is_string($requests['talent']) && trim($requests['talent']) !== '') {
            $mstTalentAggregateList = $mstTalentAggregateList->filterByTalentNameEnSlug(trim($requests['talent']));
        }
        $talentIds = $mstTalentAggregateList->getIds();
        $relYoutubeMusicVideoTalentAggregateList = $relYoutubeMusicVideoTalentAggregateList->filterByTalentIds($talentIds);
        $mstYoutubeMusicVideoAggregateList = $mstYoutubeMusicVideoAggregateList->filterByIds($relYoutubeMusicVideoTalentAggregateList->getYoutubeMusicVideoIds())
            ->sortByPublicDateDesc();

        return response()->json([
            'status' => true,
            'data' => [
                'musicList' => $mstYoutubeMusicVideoAggregateList->getAggregates()->map(function($mstYoutubeMusicVideoAggregate) use ($talentIds) {
                    return [
                        'id' => $mstYoutubeMusicVideoAggregate->getEntity()->id,
                        'title' => $mstYoutubeMusicVideoAggregate->getEntity()->music_title,
                        'talentIds' => array_map('intval', $talentIds),
                        'youtubeVideoId' => $mstYoutubeMusicVideoAggregate->getEntity()->youtube_video_code,
                        'type' => $mstYoutubeMusicVideoAggregate->getEntity()->music_type == '1' ? 'original' : 'cover',
                        'releaseDate' => $mstYoutubeMusicVideoAggregate->getEntity()->public_date,
                        'description' => $mstYoutubeMusicVideoAggregate->getEntity()->music_type == '1' ? 'オリジナル曲' : 'カバー曲',
                    ];
                })->toArray(),
            ],
        ]);
    }
}
