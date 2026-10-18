<?php

namespace App\Apis\OshiKatsuSaport;

use App\Contexts\Domain\Aggregates\MstYoutubePlaylistAggregate;
use App\Http\Controllers\Controller;
use App\Repositories\MstTalentRepository;
use App\Repositories\MstYoutubePlaylistRepository;
use App\Repositories\RelYoutubePlaylistMusicVideoRepository;
use App\Repositories\RelYoutubePlaylistMusicVideoTalentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 楽曲一覧画面に表示する YouTube プレイリスト一覧
 */
class TalentMusicPlaylistsController extends Controller
{
    private $mstTalentRepository;
    private $mstYoutubePlaylistRepository;
    private $relYoutubePlaylistMusicVideoRepository;
    private $relYoutubePlaylistMusicVideoTalentRepository;

    public function __construct(
        MstTalentRepository $mstTalentRepository,
        MstYoutubePlaylistRepository $mstYoutubePlaylistRepository,
        RelYoutubePlaylistMusicVideoRepository $relYoutubePlaylistMusicVideoRepository,
        RelYoutubePlaylistMusicVideoTalentRepository $relYoutubePlaylistMusicVideoTalentRepository
    ) {
        $this->mstTalentRepository = $mstTalentRepository;
        $this->mstYoutubePlaylistRepository = $mstYoutubePlaylistRepository;
        $this->relYoutubePlaylistMusicVideoRepository = $relYoutubePlaylistMusicVideoRepository;
        $this->relYoutubePlaylistMusicVideoTalentRepository = $relYoutubePlaylistMusicVideoTalentRepository;
    }

    /**
     * GET /oshi-katsu-saport/talent-music/playlists
     *
     * Query:
     * - talent: タレント英語名 slug（任意）。指定時はそのタレントが含まれるプレイリストのみ返す
     *
     * 表示フラグ=1 のプレイリストを登録順で返す。
     * 各プレイリストには紐付け楽曲（並び順。削除済みの楽曲は除く）と曲数を含める
     */
    public function index(Request $request): JsonResponse
    {
        $mstYoutubePlaylistAggregateList = $this->mstYoutubePlaylistRepository->all()
            ->filterByViewFlag(1);

        $talentSlug = $request->query('talent');
        if (is_string($talentSlug) && trim($talentSlug) !== '') {
            $talentIds = $this->mstTalentRepository->all()
                ->filterByTalentNameEnSlug(trim($talentSlug))
                ->getIds();
            $playlistIds = $this->relYoutubePlaylistMusicVideoTalentRepository
                ->findPlaylistIdsByTalentIds($talentIds);
            $mstYoutubePlaylistAggregateList = $mstYoutubePlaylistAggregateList->filterByIds($playlistIds);
        }

        $mstYoutubePlaylistAggregateList = $mstYoutubePlaylistAggregateList->sortByIdAsc();

        $playlistIds = $mstYoutubePlaylistAggregateList->getAggregates()
            ->map(fn (MstYoutubePlaylistAggregate $aggregate) => (int) $aggregate->getEntity()->id)
            ->values()
            ->all();
        $musicListByPlaylistId = $this->relYoutubePlaylistMusicVideoRepository
            ->findMusicListByPlaylistIds($playlistIds);

        return response()->json([
            'status' => true,
            'data' => [
                'playlists' => $mstYoutubePlaylistAggregateList->getAggregates()
                    ->map(function (MstYoutubePlaylistAggregate $aggregate) use ($musicListByPlaylistId) {
                        $musicList = $musicListByPlaylistId[(int) $aggregate->getEntity()->id] ?? [];
                        return $this->buildPlaylist($aggregate, $musicList);
                    })
                    ->values()
                    ->toArray(),
            ],
        ]);
    }

    private function buildPlaylist(MstYoutubePlaylistAggregate $aggregate, array $musicList): array
    {
        $entity = $aggregate->getEntity();
        return [
            'id' => (int) $entity->id,
            'youtubePlaylistCode' => $entity->youtube_playlist_code,
            'name' => $entity->playlist_name,
            'url' => $aggregate->getPlaylistUrl(),
            'musicCount' => count($musicList),
            'musicList' => $musicList,
        ];
    }
}
