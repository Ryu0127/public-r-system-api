<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * タレントアカウントマスタ
 */
class MstTalentAccount extends Model
{
    use HasFactory;

    protected $table = 'mst_talent_account';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
