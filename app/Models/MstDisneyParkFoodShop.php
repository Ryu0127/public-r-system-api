<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ディズニーパーク フード販売ショップマスタ
 */
class MstDisneyParkFoodShop extends Model
{
    use HasFactory;

    protected $table = 'mst_disney_park_food_shop';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $guarded = [];
    const CREATED_AT = null;
    const UPDATED_AT = null;
}
