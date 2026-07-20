<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\DisneyParkFoodMenuAggregate;
use Illuminate\Support\Collection;

class DisneyParkFoodMenuAggregateList
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

    public function firstById($id): ?DisneyParkFoodMenuAggregate
    {
        return $this->aggregates->first(function ($aggregate) use ($id) {
            return $aggregate->getEntity()->id == $id;
        });
    }

    /**
     * 公開期間内のレコードに絞る（開始・終了がNULLの場合はその側を無制限とみなす）
     */
    public function filterPublishedOn(string $date): DisneyParkFoodMenuAggregateList
    {
        return new DisneyParkFoodMenuAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($date) {
                $entity = $aggregate->getEntity();
                $startOk = $entity->publish_start_date === null || $entity->publish_start_date <= $date;
                $endOk = $entity->publish_end_date === null || $entity->publish_end_date >= $date;
                return $startOk && $endOk;
            })->values()
        );
    }

    /**
     * ショップID一覧で絞る
     *
     * @param list<int> $disneyParkFoodShopIds
     */
    public function filterByDisneyParkFoodShopIds(
        array $disneyParkFoodShopIds
    ): DisneyParkFoodMenuAggregateList {
        $idSet = array_map('intval', $disneyParkFoodShopIds);

        return new DisneyParkFoodMenuAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($idSet) {
                return in_array(
                    (int) $aggregate->getEntity()->disney_park_food_shop_id,
                    $idSet,
                    true
                );
            })->values()
        );
    }

    public function add(DisneyParkFoodMenuAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}
