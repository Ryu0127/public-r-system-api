<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\MstYoutubePlaylistAggregate;
use Illuminate\Support\Collection;

class MstYoutubePlaylistAggregateList
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

    public function filterByIds(array $ids): MstYoutubePlaylistAggregateList
    {
        $idSet = array_fill_keys(array_map('intval', $ids), true);
        return new MstYoutubePlaylistAggregateList($this->aggregates->filter(function ($aggregate) use ($idSet) {
            return isset($idSet[(int) $aggregate->getEntity()->id]);
        })->values());
    }

    public function filterByViewFlag(int $viewFlag): MstYoutubePlaylistAggregateList
    {
        return new MstYoutubePlaylistAggregateList($this->aggregates->filter(function ($aggregate) use ($viewFlag) {
            return (int) ($aggregate->getEntity()->view_flag ?? 0) === $viewFlag;
        })->values());
    }

    /**
     * 登録順（id 昇順）で並び替え
     */
    public function sortByIdAsc(): MstYoutubePlaylistAggregateList
    {
        return new MstYoutubePlaylistAggregateList(
            $this->aggregates->sortBy(function ($aggregate) {
                return (int) $aggregate->getEntity()->id;
            })->values()
        );
    }

    public function add(MstYoutubePlaylistAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}
