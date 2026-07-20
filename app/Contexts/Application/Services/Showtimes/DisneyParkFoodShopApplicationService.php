<?php

namespace App\Contexts\Application\Services\Showtimes;

use App\Contexts\Domain\Aggregates\DisneyParkFoodShopAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkFoodShopAggregateList;
use App\Repositories\MstDisneyParkFoodShopRepository;

class DisneyParkFoodShopApplicationService
{
    private MstDisneyParkFoodShopRepository $mstDisneyParkFoodShopRepository;

    public function __construct(MstDisneyParkFoodShopRepository $mstDisneyParkFoodShopRepository)
    {
        $this->mstDisneyParkFoodShopRepository = $mstDisneyParkFoodShopRepository;
    }

    public function firstById($id): ?DisneyParkFoodShopAggregate
    {
        $entity = $this->mstDisneyParkFoodShopRepository->findPk($id);
        if ($entity === null) {
            return null;
        }
        return new DisneyParkFoodShopAggregate($entity);
    }

    public function selectAll(): DisneyParkFoodShopAggregateList
    {
        return $this->mstDisneyParkFoodShopRepository->all();
    }

    public function selectByParkType(int $parkType): DisneyParkFoodShopAggregateList
    {
        return $this->mstDisneyParkFoodShopRepository->findByParkType($parkType);
    }

    public function insert(DisneyParkFoodShopAggregate $aggregate): DisneyParkFoodShopAggregate
    {
        $entity = $this->mstDisneyParkFoodShopRepository->insert($aggregate->getEntity());
        return new DisneyParkFoodShopAggregate($entity);
    }
}
