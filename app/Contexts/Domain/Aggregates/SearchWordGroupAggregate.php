<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstSearchWordGroup;

class SearchWordGroupAggregate
{
    private $entity; // MstSearchWordGroup

    public function __construct(MstSearchWordGroup $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstSearchWordGroup
    {
        return $this->entity;
    }
}

