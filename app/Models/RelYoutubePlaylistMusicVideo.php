<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * youtubeプレイリスト楽曲動画リレーション
 */
class RelYoutubePlaylistMusicVideo extends Model
{
    use HasFactory;

    protected $table = 'rel_youtube_playlist_music_video';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
