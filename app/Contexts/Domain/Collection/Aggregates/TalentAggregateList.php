<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Contexts\Domain\Aggregates\TalentGroupAggregate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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

    public function getIds(): array
    {
        $ids = [];
        foreach ($this->aggregates as $aggregate) {
            $ids[] = $aggregate->getEntity()->id;
        }
        return $ids;
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

    public function filterByTalentNameEn(string $talentNameEn): TalentAggregateList
    {
        // filter
        $filteredAggregates = $this->aggregates->filter(function ($aggregate) use ($talentNameEn) {
            return $aggregate->getEntity()->talent_name_en === $talentNameEn;
        });
        return new TalentAggregateList($filteredAggregates);
    }

    public function filterByTalentNameEnSlug(string $talentNameEn): TalentAggregateList
    {
        // filter
        $filteredAggregates = $this->aggregates->filter(function ($aggregate) use ($talentNameEn) {
            return $this->slugify($aggregate->getEntity()->talent_name_en) === $talentNameEn;
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

    public function add(TalentAggregate $aggregate)
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