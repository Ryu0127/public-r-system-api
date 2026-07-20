<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ディズニーパーク アトラクション時間帯別予想待ち時間
 */
class TblDisneyParkAttractionWaitForecast extends Model
{
    use HasFactory;

    protected $table = 'tbl_disney_park_attraction_wait_forecast';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
