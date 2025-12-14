<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\TblEventCastTalent;

class EventCastTalentAggregate
{
    private $entity; // TblEventCastTalent

    public function __construct(TblEventCastTalent $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): TblEventCastTalent
    {
        return $this->entity;
    }
}