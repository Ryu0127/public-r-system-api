<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Contexts\Domain\Aggregates\TalentGroupAggregate;
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

    public function filterByTalentGroup(TalentGroupAggregate $talentGroupAggregate, RelTalentGroupMemberAggregateList $relTalentGroupMemberAggregateList): TalentAggregateList
    {
        // find
        $talentIds = $relTalentGroupMemberAggregateList->filterByTalentGroup($talentGroupAggregate)->getTalentIds();
        // filter
        return $this->filterById($talentIds);
    }

    public function sortBySortTalentGroup(SortTalentGroupAggregateList $sortTalentGroupAggregateList): TalentAggregateList
    {
        // sort
        $sortAggregates = new Collection();
        foreach ($sortTalentGroupAggregateList->getAggregates() as $sortTalentGroupAggregate) {
            foreach ($this->aggregates as $aggregate) {
                if ($aggregate->getEntity()->id === $sortTalentGroupAggregate->getEntity()->talent_group_id) {
                    $sortAggregates->add($aggregate);
                }
            }
        }
        // other sort
        foreach ($this->aggregates as $aggregate) {
            if (!$sortAggregates->contains($aggregate)) {
                $sortAggregates->add($aggregate);
            }
        }
        return new TalentAggregateList($sortAggregates);
    }

    public function add(TalentAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}