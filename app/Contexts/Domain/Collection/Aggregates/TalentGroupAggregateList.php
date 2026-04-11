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

    public function add(TalentGroupAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}