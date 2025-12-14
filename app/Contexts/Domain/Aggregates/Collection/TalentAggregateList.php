<?php

namespace App\Contexts\Domain\Aggregates\Collection;

use App\Contexts\Domain\Aggregates\TalentAggregate;
use Illuminate\Support\Collection;

class TalentAggregateList
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

    public function getTalentNames(): array
    {
        $talentNames = [];
        foreach ($this->aggregates as $aggregate) {
            $entity = $aggregate->getEntity();
            if(empty($entity->talent_name)) continue;
            if(in_array($entity->talent_name, $talentNames)) continue;
            
            $talentNames[] = $entity->talent_name;
        }
        return $talentNames;
    }

    public function filterById(array $ids): TalentAggregateList
    {
        // filter
        $filteredAggregates = $this->aggregates->filter(function ($aggregate) use ($ids) {
            return in_array($aggregate->getEntity()->id, $ids);
        });
        return new TalentAggregateList($filteredAggregates);
    }

    public function add(TalentAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}