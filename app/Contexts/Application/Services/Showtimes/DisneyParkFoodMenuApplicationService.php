<?php

namespace App\Contexts\Application\Services\Showtimes;

use App\Contexts\Domain\Aggregates\DisneyParkFoodMenuAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkFoodMenuAggregateList;
use App\Repositories\MstDisneyParkFoodMenuRepository;

class DisneyParkFoodMenuApplicationService
{
    private MstDisneyParkFoodMenuRepository $mstDisneyParkFoodMenuRepository;

    public function __construct(MstDisneyParkFoodMenuRepository $mstDisneyParkFoodMenuRepository)
    {
        $this->mstDisneyParkFoodMenuRepository = $mstDisneyParkFoodMenuRepository;
    }

    public function firstById($id): ?DisneyParkFoodMenuAggregate
    {
        $entity = $this->mstDisneyParkFoodMenuRepository->findPk($id);
        if ($entity === null) {
            return null;
        }
        return new DisneyParkFoodMenuAggregate($entity);
    }

    public function selectAll(): DisneyParkFoodMenuAggregateList
    {
        return $this->mstDisneyParkFoodMenuRepository->all();
    }

    /**
     * @param list<int> $disneyParkFoodShopIds
     */
    public function selectByDisneyParkFoodShopIds(
        array $disneyParkFoodShopIds
    ): DisneyParkFoodMenuAggregateList {
        return $this->mstDisneyParkFoodMenuRepository
            ->findByDisneyParkFoodShopIds($disneyParkFoodShopIds);
    }

    public function insert(DisneyParkFoodMenuAggregate $aggregate): DisneyParkFoodMenuAggregate
    {
        $entity = $this->mstDisneyParkFoodMenuRepository->insert($aggregate->getEntity());
        return new DisneyParkFoodMenuAggregate($entity);
    }
}
