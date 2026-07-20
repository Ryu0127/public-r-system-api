<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstDisneyParkAttraction;

class DisneyParkAttractionAggregate
{
    private $entity; // MstDisneyParkAttraction

    public function __construct(MstDisneyParkAttraction $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstDisneyParkAttraction
    {
        return $this->entity;
    }
}
