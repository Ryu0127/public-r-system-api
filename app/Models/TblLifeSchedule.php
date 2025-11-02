<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 生活スケジュール
 */
class TblLifeSchedule extends Model
{
    use HasFactory;

    protected $table = 'tbl_life_schedule';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
