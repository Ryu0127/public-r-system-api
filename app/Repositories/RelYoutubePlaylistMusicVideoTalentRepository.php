<?php

namespace App\Repositories;

use App\Models\RelYoutubePlaylistMusicVideoTalent;

class RelYoutubePlaylistMusicVideoTalentRepository
{
    /**
     * 指定タレントが含まれるプレイリストのIDを取得
     *
     * 次の条件をすべて満たす楽曲がある場合に「含まれる」とみなす
     * - プレイリスト上でその楽曲に指定タレントが紐付いている
     * - その楽曲が現在もプレイリストに紐付いている
     * - その楽曲が削除されていない（view_flag=1）
     *
     * @param  int[] $talentIds
     * @return int[]
     */
    public function findPlaylistIdsByTalentIds(array $talentIds): array
    {
        if (count($talentIds) === 0) {
            return [];
        }

        return RelYoutubePlaylistMusicVideoTalent::query()
            ->from('rel_youtube_playlist_music_video_talent as rpt')
            ->join('rel_youtube_playlist_music_video as rpm', function ($join) {
                $join->on('rpm.youtube_playlist_id', '=', 'rpt.youtube_playlist_id')
                    ->on('rpm.youtube_music_video_id', '=', 'rpt.youtube_music_video_id');
            })
            ->join('mst_youtube_music_video as m', 'm.id', '=', 'rpt.youtube_music_video_id')
            ->whereIn('rpt.talent_id', array_map('intval', $talentIds))
            ->where('m.view_flag', 1)
            ->distinct()
            ->pluck('rpt.youtube_playlist_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
