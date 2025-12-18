<?php

namespace App\Contexts\Domain\Aggregates\Collection;

use App\Contexts\Domain\Aggregates\TalentAccountAggregate;
use Illuminate\Support\Collection;

class TalentAccountAggregateList
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
        return $this->aggregates->map(function ($aggregate) {
            return $aggregate->getEntity()->talent_id;
        })->toArray();
    }

    public function getAccountCodes(): array
    {
        return $this->aggregates->map(function ($aggregate) {
            return $aggregate->getEntity()->account_code;
        })->toArray();
    }

    public function filterByTalentId(array $talentIds): TalentAccountAggregateList
    {
        // filter
        $filteredAggregates = $this->aggregates->filter(function ($aggregate) use ($talentIds) {
            return in_array($aggregate->getEntity()->talent_id, $talentIds);
        });
        return new TalentAccountAggregateList($filteredAggregates);
    }

    public function add(TalentAccountAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}