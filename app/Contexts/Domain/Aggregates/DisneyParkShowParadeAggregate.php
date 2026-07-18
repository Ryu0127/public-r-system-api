<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstDisneyParkShowParade;

class DisneyParkShowParadeAggregate
{
    private $entity; // MstDisneyParkShowParade

    public function __construct(MstDisneyParkShowParade $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstDisneyParkShowParade
    {
        return $this->entity;
    }
}
