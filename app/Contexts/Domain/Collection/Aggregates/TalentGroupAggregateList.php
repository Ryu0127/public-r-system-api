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
    
    public function filterByTalentGroupNameEnSlug(string $talentGroupNameEn): TalentGroupAggregateList
    {
        // filter
        $filteredAggregates = $this->aggregates->filter(function ($aggregate) use ($talentGroupNameEn) {
            return $this->slugify($aggregate->getEntity()->group_name_en) === $talentGroupNameEn;
        });
        return new TalentGroupAggregateList($filteredAggregates);
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

    private function slugify(?string $raw): string
    {
        $s = strtolower(trim((string) $raw));
        $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
        return trim($s, '-');
    }
}