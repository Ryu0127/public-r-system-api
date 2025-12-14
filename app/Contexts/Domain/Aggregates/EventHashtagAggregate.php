<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\TblEventHashtag;

class EventHashtagAggregate
{
    private $entity; // TblEventHashtag

    public function __construct(TblEventHashtag $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): TblEventHashtag
    {
        return $this->entity;
    }
}