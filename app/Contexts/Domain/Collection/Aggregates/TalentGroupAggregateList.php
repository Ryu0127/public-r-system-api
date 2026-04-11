<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\TalentGroupAggregate;
use Illuminate\Support\Collection;

class TalentGroupAggregateList
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

    public function sortBySortTalentGroup(SortTalentGroupAggregateList $sortTalentGroupAggregateList): TalentGroupAggregateList
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
        return new TalentGroupAggregateList($sortAggregates);
    }

    public function add(TalentGroupAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}