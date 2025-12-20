<?php

namespace App\Contexts\Domain\Aggregates\Collection;

use App\Contexts\Domain\Aggregates\TalentSearchWordAggregate;
use Illuminate\Support\Collection;

class TalentSearchWordAggregateList
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

    public function getSearchWordGroupIds(): array
    {
        $searchWordGroupIds = [];
        foreach ($this->aggregates as $aggregate) {
            $entity = $aggregate->getEntity();
            if(empty($entity->search_word_group_id)) continue;
            if(in_array($entity->search_word_group_id, $searchWordGroupIds)) continue;
            
            $searchWordGroupIds[] = $entity->search_word_group_id;
        }
        return $searchWordGroupIds;
    }

    public function filterByTalentId(string $talentId): TalentSearchWordAggregateList
    {
        // filter
        $filteredAggregates = $this->aggregates->filter(function ($aggregate) use ($talentId) {
            return $aggregate->getEntity()->talent_id == $talentId;
        })->values();
        return new TalentSearchWordAggregateList($filteredAggregates);
    }

    public function filterBySearchWordGroupId(string $searchWordGroupId): TalentSearchWordAggregateList
    {
        // filter
        $filteredAggregates = $this->aggregates->filter(function ($aggregate) use ($searchWordGroupId) {
            return $aggregate->getEntity()->search_word_group_id == $searchWordGroupId;
        })->values();
        return new TalentSearchWordAggregateList($filteredAggregates);
    }

    public function add(TalentSearchWordAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}

