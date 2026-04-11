<?php

namespace App\Apis\OshiKatsuSaport;

use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Contexts\Domain\Aggregates\TalentGroupAggregate;
use App\Http\Controllers\Controller;
use App\Models\RelYoutubeMusicVideoTalent;
use App\Repositories\MstTalentRepository;
use App\Repositories\MstTalentGroupRepository;
use App\Repositories\RelTalentGroupMemberRepository;
use App\Repositories\SortTalentGroupRepository;
use Illuminate\Http\JsonResponse;

class TalentMusicTalentsController extends Controller
{

    private $talentRepository;
    private $talentGroupRepository;
    private $relTalentGroupMemberRepository;
    private $sortTalentGroupRepository;

    public function __construct(
        MstTalentRepository $talentRepository,
        MstTalentGroupRepository $talentGroupRepository,
        RelTalentGroupMemberRepository $relTalentGroupMemberRepository,
        SortTalentGroupRepository $sortTalentGroupRepository,
    ) {
        $this->talentRepository = $talentRepository;
        $this->talentGroupRepository = $talentGroupRepository;
        $this->relTalentGroupMemberRepository = $relTalentGroupMemberRepository;
        $this->sortTalentGroupRepository = $sortTalentGroupRepository;
    }

    private function slugify(?string $raw): string
    {
        $s = strtolower(trim((string) $raw));
        $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
        return trim($s, '-');
    }

    /**
     * タレント別楽曲一覧（タレント選択UI用）タレント一覧取得API
     * GET /oshi-katsu-saport/talent-music/talents
     */
    public function index(): JsonResponse
    {
        // view_flag=1 の楽曲に紐づく talent_id のみ返す
        $talentIds = RelYoutubeMusicVideoTalent::query()
            ->join(
                'mst_youtube_music_video',
                'rel_youtube_music_video_talent.youtube_music_video_id',
                '=',
                'mst_youtube_music_video.id'
            )
            ->where('mst_youtube_music_video.view_flag', 1)
            ->distinct()
            ->pluck('rel_youtube_music_video_talent.talent_id')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();
        $talentAggregateList = $this->talentRepository->all();
        $talentGroupAggregateList = $this->talentGroupRepository->getByTalentIds($talentIds);
        $relTalentGroupMemberAggregateList = $this->relTalentGroupMemberRepository->all();
        $sortTalentGroupAggregateList = $this->sortTalentGroupRepository->getBySortType('1');

        // filter
        $talentAggregateList = $talentAggregateList->filterById($talentIds);

        // sort
        $talentGroupAggregateList = $talentGroupAggregateList->sortBySortTalentGroup($sortTalentGroupAggregateList);

        return response()->json([
            'status' => true,
            'data' => [
                'talents' => $talentAggregateList->getAggregates()->map(fn (TalentAggregate $aggregate) => [
                    'id' => $aggregate->getEntity()->id,
                    'talentName' => $aggregate->getEntity()->talent_name,
                    'talentNameEn' => $aggregate->getEntity()->talent_name_en,
                    'talentSlug' => $this->slugify($aggregate->getEntity()->talent_name_en),
                    'iconImgUrl' => $aggregate->getEntity()->icon_img_url,
                ])->values(),
                'groups' => $talentGroupAggregateList->getAggregates()->map(fn (TalentGroupAggregate $aggregate) => [
                    'groupId' => $aggregate->getEntity()->id,
                    'groupName' => $aggregate->getEntity()->group_name,
                    'groupNameEn' => $aggregate->getEntity()->group_name_en,
                    'groupSlug' => $this->slugify($aggregate->getEntity()->group_name_en),
                    'talents' => $talentAggregateList->filterByTalentGroup($aggregate, $relTalentGroupMemberAggregateList)->getAggregates()->map(fn (TalentAggregate $aggregate) => [
                        'id' => $aggregate->getEntity()->id,
                        'talentName' => $aggregate->getEntity()->talent_name,
                        'talentNameEn' => $aggregate->getEntity()->talent_name_en,
                        'talentSlug' => $this->slugify($aggregate->getEntity()->talent_name_en),
                        'iconImgUrl' => $aggregate->getEntity()->icon_img_url,
                    ])->values()
                ])->values(),
            ],
        ]);
    }
}

