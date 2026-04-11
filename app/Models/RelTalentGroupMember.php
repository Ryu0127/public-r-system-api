<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * タレントグループメンバーリレーション
 */
class RelTalentGroupMember extends Model
{
    use HasFactory;

    protected $table = 'rel_talent_group_member';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
