<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ディズニーパーク ショー・パレード公演時刻
 */
class TblDisneyParkShowParadeSchedule extends Model
{
    use HasFactory;

    protected $table = 'tbl_disney_park_show_parade_schedule';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
