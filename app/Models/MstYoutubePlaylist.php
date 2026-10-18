<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * youtubeプレイリストマスタ
 */
class MstYoutubePlaylist extends Model
{
    use HasFactory;

    protected $table = 'mst_youtube_playlist';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
