<?php

namespace App\Contexts\Domain\Aggregates\Collection;

use App\Contexts\Domain\Aggregates\EventCastTalentAggregate;
use Illuminate\Support\Collection;

class EventCastTalentAggregateList
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
        $eventIds = [];
        foreach ($this->aggregates as $aggregate) {
            $entity = $aggregate->getEntity();
            if(empty($entity->event_id)) continue;
            if(in_array($entity->event_id, $eventIds)) continue;
            
            $eventIds[] = $entity->event_id;
        }
        return $eventIds;
    }

    public function getTalentIds(): array
    {
        $talentIds = [];
        foreach ($this->aggregates as $aggregate) {
            $entity = $aggregate->getEntity();
            if(empty($entity->talent_id)) continue;
            if(in_array($entity->talent_id, $talentIds)) continue;
            
            $talentIds[] = $entity->talent_id;
        }
        return $talentIds;
    }

    public function filterByEventId(string $eventId): EventCastTalentAggregateList
    {
        // filter
        $filteredAggregates = $this->aggregates->filter(function ($aggregate) use ($eventId) {
            return $aggregate->getEntity()->event_id == $eventId;
        });
        return new EventCastTalentAggregateList($filteredAggregates);
    }

    public function add(EventCastTalentAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}