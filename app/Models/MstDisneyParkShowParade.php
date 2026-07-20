<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ディズニーパーク ショー・パレードマスタ
 */
class MstDisneyParkShowParade extends Model
{
    use HasFactory;

    protected $table = 'mst_disney_park_show_parade';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
