<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\SortTalentGroupAggregate;
use Illuminate\Support\Collection;

class SortTalentGroupAggregateList
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

    public function add(SortTalentGroupAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}
