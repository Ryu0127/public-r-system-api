<?php

namespace App\Contexts\Domain\Aggregates\Collection;

use App\Contexts\Domain\Aggregates\SearchWordGroupAggregate;
use Illuminate\Support\Collection;

class SearchWordGroupAggregateList
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

    public function firstById(string $id): ?SearchWordGroupAggregate
    {
        return $this->aggregates->first(function ($aggregate) use ($id) {
            return $aggregate->getEntity()->id == $id;
        });
    }

    public function add(SearchWordGroupAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }

    public function sortBySortOrderDesc(): SearchWordGroupAggregateList
    {
        $sorted = $this->aggregates->sortBy(function ($aggregate) {
            return $aggregate->getEntity()->sort_order;
        })->values();
        
        return new SearchWordGroupAggregateList($sorted);
    }
}
