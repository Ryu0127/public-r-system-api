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

    public function filterNotEntryOrCloseEvent(): EventAggregateList
    {
        return new EventAggregateList($this->aggregates->filter(function ($aggregate) {
            $isEntryEvent = in_array($aggregate->getEntity()->event_type_id, [5, 6]);
            $isCloseEvent = $aggregate->getEntity()->event_end_date < now();
            return !$isEntryEvent || !($isEntryEvent && $isCloseEvent);
        }));
    }

    public function add(EventAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}