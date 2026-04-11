<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\RelTalentGroupMemberAggregate;
use App\Contexts\Domain\Aggregates\TalentGroupAggregate;
use Illuminate\Support\Collection;

class RelTalentGroupMemberAggregateList
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
            $talentIds[] = $aggregate->getEntity()->talent_id;
        }
        return $talentIds;
    }

    public function add(RelTalentGroupMemberAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }

    public function filterByTalentGroup(TalentGroupAggregate $talentGroupAggregate): RelTalentGroupMemberAggregateList
    {
        // filter
        $filteredAggregates = $this->aggregates->filter(function ($aggregate) use ($talentGroupAggregate) {
            return $aggregate->getEntity()->talent_group_id === $talentGroupAggregate->getEntity()->id;
        });
        return new RelTalentGroupMemberAggregateList($filteredAggregates);
    }
}