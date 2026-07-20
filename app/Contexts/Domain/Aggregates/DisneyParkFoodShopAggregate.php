<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstDisneyParkFoodShop;

class DisneyParkFoodShopAggregate
{
    private $entity; // MstDisneyParkFoodShop

    public function __construct(MstDisneyParkFoodShop $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstDisneyParkFoodShop
    {
        return $this->entity;
    }
}
