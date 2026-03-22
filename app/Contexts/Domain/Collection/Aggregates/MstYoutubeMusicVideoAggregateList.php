<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\MstYoutubeMusicVideoAggregate;
use Illuminate\Support\Collection;

class MstYoutubeMusicVideoAggregateList
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

    public function filterByIds(array $ids): MstYoutubeMusicVideoAggregateList
    {
        return new MstYoutubeMusicVideoAggregateList($this->aggregates->filter(function ($aggregate) use ($ids) {
            return in_array($aggregate->getEntity()->id, $ids);
        }));
    }

    public function firstById(string $id): ?MstYoutubeMusicVideoAggregate
    {
        return $this->aggregates->first(function ($aggregate) use ($id) {
            return $aggregate->getEntity()->id == $id;
        });
    }

    public function add(MstYoutubeMusicVideoAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}