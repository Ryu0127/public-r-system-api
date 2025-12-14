<?php

namespace App\Contexts\Domain\Aggregates\Collection;

use App\Contexts\Domain\Aggregates\EventTypeAggregate;
use Illuminate\Support\Collection;

class EventTypeAggregateList
{
    private $aggregates; // Collection

    public function __construct(Collection $aggregates)
    {
        $this->aggregates = $aggregates;
    }

    public function getAggregates(): Collection
    {
        return $this->aggregates;
    }

    public function firstById(string $id): ?EventTypeAggregate
    {
        return $this->aggregates->first(function ($aggregate) use ($id) {
            return $aggregate->getEntity()->id == $id;
        });
    }

    public function add(EventTypeAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}