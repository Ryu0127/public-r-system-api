<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\SortTalentGroup;

class SortTalentGroupAggregate
{
    private $entity; // SortTalentGroup

    public function __construct(SortTalentGroup $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): SortTalentGroup
    {
        return $this->entity;
    }
}

