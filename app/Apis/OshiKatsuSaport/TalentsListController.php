<?php

namespace App\Apis\OshiKatsuSaport;

use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Contexts\Domain\Aggregates\TalentGroupAggregate;
use App\Http\Controllers\Controller;
use App\Repositories\MstTalentRepository;
use App\Repositories\MstTalentGroupRepository;
use App\Repositories\RelTalentGroupMemberRepository;
use App\Repositories\SortTalentGroupRepository;
use Illuminate\Http\JsonResponse;

/**
 * 画面共通のタレント一覧取得API
 * 特定機能（楽曲・ハッシュタグ等）に依存せず、全タレントをグループ情報付きで返す
 */
class TalentsListController extends Controller
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
     * タレント一覧取得API（画面共通）
     * GET /oshi-katsu-saport/talents
     */
    public function index(): JsonResponse
    {
        $talentAggregateList = $this->talentRepository->all();
        $talentIds = $talentAggregateList->getIds();
        $talentGroupAggregateList = $this->talentGroupRepository->getByTalentIds($talentIds);
        $relTalentGroupMemberAggregateList = $this->relTalentGroupMemberRepository->all();
        $sortTalentGroupAggregateList = $this->sortTalentGroupRepository->getBySortType('1');

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
