<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstTalent;

class TalentAggregate
{
    private $entity; // MstTalent

    public function __construct(MstTalent $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstTalent
    {
        return $this->entity;
    }
}