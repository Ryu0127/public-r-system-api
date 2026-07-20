<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ユーザー別ディズニーパークお気に入り
 */
class TblUserDisneyParkFavorite extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_disney_park_favorite';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
