<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * youtubeプレイリスト楽曲動画タレントリレーション
 * （プレイリストに紐付けた楽曲ごとのタレント。プレイリストID + 楽曲ID で楽曲を特定する）
 */
class RelYoutubePlaylistMusicVideoTalent extends Model
{
    use HasFactory;

    protected $table = 'rel_youtube_playlist_music_video_talent';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
