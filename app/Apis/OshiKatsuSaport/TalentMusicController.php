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
 */
class TalentMusicController extends Controller
{
    private const DEFAULT_PER_PAGE = 20;
    private const MAX_PER_PAGE = 100;

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
     *
     * Query:
     * - talent: タレント英語名 slug（任意）
     * - group: グループ英語名 slug（任意）
     * - page: ページ番号（デフォルト 1）
     * - perPage: 1ページ件数（デフォルト 20、最大 100）
     */
    public function index(Request $request): JsonResponse
    {
        $requests = [
            'group' => $request->query('group'),
            'talent' => $request->query('talent'),
        ];
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(
            max((int) $request->query('perPage', self::DEFAULT_PER_PAGE), 1),
            self::MAX_PER_PAGE
        );

        $mstTalentGroupAggregateList = $this->mstTalentGroupRepository->all();
        $mstTalentAggregateList = $this->mstTalentRepository->all();
        $mstYoutubeMusicVideoAggregateList = $this->mstYoutubeMusicVideoRepository->all();
        $relTalentGroupMemberAggregateList = $this->relTalentGroupMemberRepository->all();
        $relYoutubeMusicVideoTalentAggregateList = $this->relYoutubeMusicVideoTalentRepository->all();
        $allRelYoutubeMusicVideoTalentAggregateList = $relYoutubeMusicVideoTalentAggregateList;

        if (is_string($requests['group']) && trim($requests['group']) !== '') {
            $mstTalentGroupAggregateList = $mstTalentGroupAggregateList->filterByTalentGroupNameEnSlug(trim($requests['group']));
            $mstTalentGroupAggregate = $mstTalentGroupAggregateList->getAggregates()->first();
            $relTalentGroupMemberAggregateList = $relTalentGroupMemberAggregateList->filterByTalentGroup($mstTalentGroupAggregate);
            $mstTalentAggregateList = $mstTalentAggregateList->filterByTalentGroup($mstTalentGroupAggregate, $relTalentGroupMemberAggregateList);
            $talentIds = $mstTalentAggregateList->getIds();
            $relYoutubeMusicVideoTalentAggregateList = $relYoutubeMusicVideoTalentAggregateList->filterMusicByTalentIds($talentIds);
        }
        if (is_string($requests['talent']) && trim($requests['talent']) !== '') {
            $mstTalentAggregateList = $mstTalentAggregateList->filterByTalentNameEnSlug(trim($requests['talent']));
            $talentIds = $mstTalentAggregateList->getIds();
            $relYoutubeMusicVideoTalentAggregateList = $relYoutubeMusicVideoTalentAggregateList->filterByTalentIds($talentIds);
        }

        $musicVideoIds = $relYoutubeMusicVideoTalentAggregateList->getYoutubeMusicVideoIds();
        $mstYoutubeMusicVideoAggregateList = $mstYoutubeMusicVideoAggregateList
            ->filterByIds($musicVideoIds)
            ->filterByViewFlag(1)
            ->sortByPublicDateDesc();

        $talentIdsByMusicId = [];
        foreach ($allRelYoutubeMusicVideoTalentAggregateList->getAggregates() as $relAggregate) {
            $entity = $relAggregate->getEntity();
            $musicId = (int) $entity->youtube_music_video_id;
            $talentIdsByMusicId[$musicId][] = (int) $entity->talent_id;
        }

        $allAggregates = $mstYoutubeMusicVideoAggregateList->getAggregates()->values();
        $total = $allAggregates->count();
        $originalCount = $allAggregates->filter(function ($aggregate) {
            return $aggregate->getEntity()->music_type == '1';
        })->count();
        $coverCount = $total - $originalCount;
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $from = $total > 0 ? (($page - 1) * $perPage) + 1 : null;
        $to = $total > 0 ? min($page * $perPage, $total) : null;
        $pageAggregates = $allAggregates->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'status' => true,
            'data' => [
                'counts' => [
                    'all' => $total,
                    'original' => $originalCount,
                    'cover' => $coverCount,
                ],
                'pagination' => [
                    'total' => $total,
                    'perPage' => $perPage,
                    'currentPage' => $page,
                    'lastPage' => $lastPage,
                    'from' => $from,
                    'to' => $to,
                ],
                'musicList' => $pageAggregates->map(function ($mstYoutubeMusicVideoAggregate) use ($talentIdsByMusicId) {
                    $entity = $mstYoutubeMusicVideoAggregate->getEntity();
                    $musicId = (int) $entity->id;
                    $talentIds = array_values(array_unique($talentIdsByMusicId[$musicId] ?? []));

                    return [
                        'id' => $entity->id,
                        'title' => $entity->music_title,
                        'talentIds' => $talentIds,
                        'youtubeVideoId' => $entity->youtube_video_code,
                        'type' => $entity->music_type == '1' ? 'original' : 'cover',
                        'releaseDate' => $entity->public_date,
                        'description' => $entity->music_type == '1' ? 'オリジナル曲' : 'カバー曲',
                    ];
                })->toArray(),
            ],
        ]);
    }
}
