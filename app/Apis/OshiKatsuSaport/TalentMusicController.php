<?php

namespace App\Apis\OshiKatsuSaport;

use App\Http\Controllers\Controller;
use App\Models\MstTalent;
use App\Repositories\MstYoutubeMusicVideoRepository;
use App\Repositories\RelYoutubeMusicVideoTalentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * タレント別楽曲一覧（フロント talent-music 画面用）
 * DB 未接続のためレスポンスは仮データ固定
 */
class TalentMusicController extends Controller
{
    private $mstYoutubeMusicVideoRepository;
    private $relYoutubeMusicVideoTalentRepository;

    public function __construct(
        MstYoutubeMusicVideoRepository $mstYoutubeMusicVideoRepository,
        RelYoutubeMusicVideoTalentRepository $relYoutubeMusicVideoTalentRepository,
    ){
        $this->mstYoutubeMusicVideoRepository = $mstYoutubeMusicVideoRepository;
        $this->relYoutubeMusicVideoTalentRepository = $relYoutubeMusicVideoTalentRepository;
    }

    /**
     * GET /oshi-katsu-saport/talent-music
     *
     * クエリ talent_ids: カンマ区切り（1,2,3）または talent_ids[] の繰り返しで複数タレント指定可。
     * ID が空のときは musicList は空配列。
     */
    public function index(Request $request): JsonResponse
    {
        $talentNameEn = $request->query('talent');
        if (is_string($talentNameEn) && trim($talentNameEn) !== '') {
            $talentSlug = trim($talentNameEn);
            $talent = MstTalent::query()
                ->get(['id', 'talent_name_en'])
                ->first(function ($t) use ($talentSlug) {
                    return $this->slugify($t->talent_name_en) === $talentSlug;
                });
            if (!$talent) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'musicList' => [],
                    ],
                ]);
            }
            $talentIds = [(string) $talent->id];
        } else {
            $talentIds = $this->normalizeTalentIds($request);
        }

        $relYoutubeMusicVideoTalentAggregateList = $this->relYoutubeMusicVideoTalentRepository
            ->all()
            ->filterByTalentIds($talentIds);
        $mstYoutubeMusicVideoAggregateList = $this->mstYoutubeMusicVideoRepository
            ->all()
            ->filterByIds($relYoutubeMusicVideoTalentAggregateList->getYoutubeMusicVideoIds())
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

    /**
     * talent_ids / talentIds のクエリ値を配列で返す（カンマ区切り文字列は explode のみ）。
     */
    private function normalizeTalentIds(Request $request): array
    {
        $raw = $request->query('talent_ids', $request->query('talentIds'));
        if (is_string($raw)) {
            return array_values(explode(',', $raw));
        }
        if (! is_array($raw)) {
            return [];
        }

        return array_values($raw);
    }

    private function slugify(?string $raw): string
    {
        $s = strtolower(trim((string) $raw));
        $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
        return trim($s, '-');
    }
}
