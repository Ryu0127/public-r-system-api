<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * youtube楽曲動画出演タレントリレーション
 */
class RelYoutubeMusicVideoTalent extends Model
{
    use HasFactory;

    protected $table = 'rel_youtube_music_video_talent';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
