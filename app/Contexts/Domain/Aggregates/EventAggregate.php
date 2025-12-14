<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\TblEvent;

class EventAggregate
{
    private $entity; // TblEvent

    public function __construct(TblEvent $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): TblEvent
    {
        return $this->entity;
    }
}