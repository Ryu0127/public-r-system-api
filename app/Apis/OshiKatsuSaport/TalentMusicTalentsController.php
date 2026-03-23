<?php

namespace App\Apis\OshiKatsuSaport;

use App\Http\Controllers\Controller;
use App\Models\MstTalent;
use App\Models\RelYoutubeMusicVideoTalent;
use Illuminate\Http\JsonResponse;

class TalentMusicTalentsController extends Controller
{
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

        $talents = MstTalent::query()
            ->whereIn('id', $talentIds)
            ->get(['id', 'talent_name', 'talent_name_en']);

        return response()->json([
            'status' => true,
            'data' => [
                'talents' => $talents->map(fn ($t) => [
                    'id' => $t->id,
                    'talentName' => $t->talent_name,
                    'talentNameEn' => $t->talent_name_en,
                ])->values(),
            ],
        ]);
    }
}

