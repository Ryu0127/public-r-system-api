<?php

namespace App\Repositories;

use App\Models\RelYoutubePlaylistMusicVideo;

class RelYoutubePlaylistMusicVideoRepository
{
    /**
     * プレイリストごとの紐付け楽曲（並び順 → ID順。削除済み（view_flag=0）の楽曲は除く）
     *
     * @param  int[] $playlistIds
     * @return array<int, array<int, array{id: int, title: string, youtubeVideoId: string}>> プレイリストID → 楽曲一覧
     */
    public function findMusicListByPlaylistIds(array $playlistIds): array
    {
        if (count($playlistIds) === 0) {
            return [];
        }

        $rows = RelYoutubePlaylistMusicVideo::query()
            ->from('rel_youtube_playlist_music_video as rpm')
            ->join('mst_youtube_music_video as m', 'm.id', '=', 'rpm.youtube_music_video_id')
            ->whereIn('rpm.youtube_playlist_id', array_map('intval', $playlistIds))
            ->where('m.view_flag', 1)
            ->orderBy('rpm.youtube_playlist_id')
            ->orderBy('rpm.sort')
            ->orderBy('rpm.id')
            ->get([
                'rpm.youtube_playlist_id as playlist_id',
                'm.id as music_id',
                'm.music_title as music_title',
                'm.youtube_video_code as youtube_video_code',
            ]);

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row->playlist_id][] = [
                'id' => (int) $row->music_id,
                'title' => (string) ($row->music_title ?? ''),
                'youtubeVideoId' => (string) ($row->youtube_video_code ?? ''),
            ];
        }
        return $map;
    }
}
