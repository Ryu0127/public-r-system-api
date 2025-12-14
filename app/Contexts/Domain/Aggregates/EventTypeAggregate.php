<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstEventType;

class EventTypeAggregate
{
    private $entity; // MstEventType

    public function __construct(MstEventType $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstEventType
    {
        return $this->entity;
    }
}