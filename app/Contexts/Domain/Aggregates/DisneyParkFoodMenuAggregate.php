<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstDisneyParkFoodMenu;

class DisneyParkFoodMenuAggregate
{
    private $entity; // MstDisneyParkFoodMenu

    public function __construct(MstDisneyParkFoodMenu $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstDisneyParkFoodMenu
    {
        return $this->entity;
    }
}
