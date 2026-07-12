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
        $idSet = array_fill_keys(array_map('intval', $ids), true);
        return new MstYoutubeMusicVideoAggregateList($this->aggregates->filter(function ($aggregate) use ($idSet) {
            return isset($idSet[(int) $aggregate->getEntity()->id]);
        })->values());
    }

    public function filterByViewFlag(int $viewFlag): MstYoutubeMusicVideoAggregateList
    {
        return new MstYoutubeMusicVideoAggregateList($this->aggregates->filter(function ($aggregate) use ($viewFlag) {
            return (int) ($aggregate->getEntity()->view_flag ?? 0) === $viewFlag;
        })->values());
    }

    public function sortByPublicDateDesc(): MstYoutubeMusicVideoAggregateList
    {
        return new MstYoutubeMusicVideoAggregateList(
            $this->aggregates->sortByDesc(function ($aggregate) {
                return $aggregate->getEntity()->public_date;
            })->values()
        );
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