<?php

namespace App\Contexts\Domain\Aggregates\Collection;

use App\Contexts\Domain\Aggregates\EventHashtagAggregate;
use Illuminate\Support\Collection;

class EventHashtagAggregateList
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

    public function getEventIds(): array
    {
        return $this->aggregates->map(function ($aggregate) {
            return $aggregate->getEntity()->event_id;
        })->toArray();
    }

    public function getHashtags(): array
    {
        return $this->aggregates->map(function ($aggregate) {
            return $aggregate->getEntity()->hashtag;
        })->toArray();
    }

    public function fillterByEventId(string $eventId): EventHashtagAggregateList
    {
        $aggregates = $this->aggregates->filter(function ($aggregate) use ($eventId) {
            return $aggregate->getEntity()->event_id == $eventId;
        });
        return new EventHashtagAggregateList($aggregates);
    }

    public function add(EventHashtagAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}