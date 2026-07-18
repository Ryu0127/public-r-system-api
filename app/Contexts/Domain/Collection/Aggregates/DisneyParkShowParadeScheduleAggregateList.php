<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\DisneyParkShowParadeScheduleAggregate;
use Illuminate\Support\Collection;

class DisneyParkShowParadeScheduleAggregateList
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
        return $this->aggregates->map(function ($aggregate) {
            return $aggregate->getEntity()->id;
        })->toArray();
    }

    public function firstById($id): ?DisneyParkShowParadeScheduleAggregate
    {
        return $this->aggregates->first(function ($aggregate) use ($id) {
            return $aggregate->getEntity()->id == $id;
        });
    }

    /**
     * 中止以外に絞る
     */
    public function filterNotCanceled(): DisneyParkShowParadeScheduleAggregateList
    {
        return new DisneyParkShowParadeScheduleAggregateList(
            $this->aggregates->filter(function ($aggregate) {
                return (int) $aggregate->getEntity()->cancel_flag !== 1;
            })->values()
        );
    }

    /**
     * ショー・パレードIDで絞る
     */
    public function filterByShowParadeId(int $showParadeId): DisneyParkShowParadeScheduleAggregateList
    {
        return new DisneyParkShowParadeScheduleAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($showParadeId) {
                return (int) $aggregate->getEntity()->show_parade_id === $showParadeId;
            })->values()
        );
    }

    /**
     * ショー・パレードID一覧で絞る
     *
     * @param list<int> $showParadeIds
     */
    public function filterByShowParadeIds(array $showParadeIds): DisneyParkShowParadeScheduleAggregateList
    {
        $idSet = array_map('intval', $showParadeIds);

        return new DisneyParkShowParadeScheduleAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($idSet) {
                return in_array((int) $aggregate->getEntity()->show_parade_id, $idSet, true);
            })->values()
        );
    }

    public function add(DisneyParkShowParadeScheduleAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}
