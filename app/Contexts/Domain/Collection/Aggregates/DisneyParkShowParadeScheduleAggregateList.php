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
    public function filterByDisneyParkShowParadeId(int $disneyParkShowParadeId): DisneyParkShowParadeScheduleAggregateList
    {
        return new DisneyParkShowParadeScheduleAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($disneyParkShowParadeId) {
                return (int) $aggregate->getEntity()->disney_park_show_parade_id === $disneyParkShowParadeId;
            })->values()
        );
    }

    /**
     * ショー・パレードID一覧で絞る
     *
     * @param list<int> $disneyParkShowParadeIds
     */
    public function filterByDisneyParkShowParadeIds(array $disneyParkShowParadeIds): DisneyParkShowParadeScheduleAggregateList
    {
        $idSet = array_map('intval', $disneyParkShowParadeIds);

        return new DisneyParkShowParadeScheduleAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($idSet) {
                return in_array((int) $aggregate->getEntity()->disney_park_show_parade_id, $idSet, true);
            })->values()
        );
    }

    public function add(DisneyParkShowParadeScheduleAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}
