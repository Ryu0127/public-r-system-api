<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\DisneyParkFoodShopAggregate;
use Illuminate\Support\Collection;

class DisneyParkFoodShopAggregateList
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

    public function firstById($id): ?DisneyParkFoodShopAggregate
    {
        return $this->aggregates->first(function ($aggregate) use ($id) {
            return $aggregate->getEntity()->id == $id;
        });
    }

    /**
     * 公開期間内のレコードに絞る（開始・終了がNULLの場合はその側を無制限とみなす）
     */
    public function filterPublishedOn(string $date): DisneyParkFoodShopAggregateList
    {
        return new DisneyParkFoodShopAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($date) {
                $entity = $aggregate->getEntity();
                $startOk = $entity->publish_start_date === null || $entity->publish_start_date <= $date;
                $endOk = $entity->publish_end_date === null || $entity->publish_end_date >= $date;
                return $startOk && $endOk;
            })->values()
        );
    }

    public function filterByParkType(int $parkType): DisneyParkFoodShopAggregateList
    {
        return new DisneyParkFoodShopAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($parkType) {
                return (int) $aggregate->getEntity()->park_type === $parkType;
            })->values()
        );
    }

    public function add(DisneyParkFoodShopAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}
