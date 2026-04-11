<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstTalentGroup;

class TalentGroupAggregate
{
    private $entity; // MstTalentGroup

    public function __construct(MstTalentGroup $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstTalentGroup
    {
        return $this->entity;
    }
}