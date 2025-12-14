<?php

namespace App\Contexts\Domain\Aggregates\Collection;

use App\Contexts\Domain\Aggregates\EventAggregate;
use Illuminate\Support\Collection;

class EventAggregateList
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

    public function getIds(): array
    {
        return $this->aggregates->map(function ($aggregate) {
            return $aggregate->getEntity()->id;
        })->toArray();
    }

    public function add(EventAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}