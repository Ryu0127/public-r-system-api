<?php

namespace App\Contexts\Domain\Aggregates\Collection;

use App\Contexts\Domain\Aggregates\TalentHashtagAggregate;
use Illuminate\Support\Collection;

class TalentHashtagAggregateList
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

    public function add(TalentHashtagAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}