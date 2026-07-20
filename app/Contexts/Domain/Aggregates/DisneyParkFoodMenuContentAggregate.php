<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstDisneyParkFoodMenuContent;

class DisneyParkFoodMenuContentAggregate
{
    private $entity; // MstDisneyParkFoodMenuContent

    public function __construct(MstDisneyParkFoodMenuContent $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstDisneyParkFoodMenuContent
    {
        return $this->entity;
    }
}
