<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 生活スケジュール通知
 */
class TblLifeScheduleNotification extends Model
{
    use HasFactory;

    protected $table = 'tbl_life_schedule_notification';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
