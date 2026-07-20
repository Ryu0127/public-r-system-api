<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\DisneyParkFoodMenuContentAggregate;
use Illuminate\Support\Collection;

class DisneyParkFoodMenuContentAggregateList
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

    public function firstById($id): ?DisneyParkFoodMenuContentAggregate
    {
        return $this->aggregates->first(function ($aggregate) use ($id) {
            return $aggregate->getEntity()->id == $id;
        });
    }

    /**
     * メニューID一覧で絞る
     *
     * @param list<int> $disneyParkFoodMenuIds
     */
    public function filterByDisneyParkFoodMenuIds(
        array $disneyParkFoodMenuIds
    ): DisneyParkFoodMenuContentAggregateList {
        $idSet = array_map('intval', $disneyParkFoodMenuIds);

        return new DisneyParkFoodMenuContentAggregateList(
            $this->aggregates->filter(function ($aggregate) use ($idSet) {
                return in_array(
                    (int) $aggregate->getEntity()->disney_park_food_menu_id,
                    $idSet,
                    true
                );
            })->values()
        );
    }

    /**
     * メニューIDごとに内容配列を組み立てる
     *
     * @return array<int, list<array{id:int, contentName:string, pauseFlag:int}>>
     */
    public function groupContentsByMenuId(): array
    {
        $grouped = [];
        foreach ($this->aggregates as $aggregate) {
            $entity = $aggregate->getEntity();
            $menuId = (int) $entity->disney_park_food_menu_id;
            if (!isset($grouped[$menuId])) {
                $grouped[$menuId] = [];
            }
            $grouped[$menuId][] = [
                'id' => (int) $entity->id,
                'contentName' => $entity->content_name,
                'pauseFlag' => (int) $entity->pause_flag,
            ];
        }
        return $grouped;
    }

    public function add(DisneyParkFoodMenuContentAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}
