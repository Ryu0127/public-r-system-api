<?php

namespace App\Contexts\Application\Services\Showtimes;

use App\Contexts\Domain\Aggregates\DisneyParkFoodMenuContentAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkFoodMenuContentAggregateList;
use App\Repositories\MstDisneyParkFoodMenuContentRepository;

class DisneyParkFoodMenuContentApplicationService
{
    private MstDisneyParkFoodMenuContentRepository $mstDisneyParkFoodMenuContentRepository;

    public function __construct(
        MstDisneyParkFoodMenuContentRepository $mstDisneyParkFoodMenuContentRepository
    ) {
        $this->mstDisneyParkFoodMenuContentRepository = $mstDisneyParkFoodMenuContentRepository;
    }

    public function firstById($id): ?DisneyParkFoodMenuContentAggregate
    {
        $entity = $this->mstDisneyParkFoodMenuContentRepository->findPk($id);
        if ($entity === null) {
            return null;
        }
        return new DisneyParkFoodMenuContentAggregate($entity);
    }

    public function selectAll(): DisneyParkFoodMenuContentAggregateList
    {
        return $this->mstDisneyParkFoodMenuContentRepository->all();
    }

    /**
     * @param list<int> $disneyParkFoodMenuIds
     */
    public function selectByDisneyParkFoodMenuIds(
        array $disneyParkFoodMenuIds
    ): DisneyParkFoodMenuContentAggregateList {
        return $this->mstDisneyParkFoodMenuContentRepository
            ->findByDisneyParkFoodMenuIds($disneyParkFoodMenuIds);
    }

    public function insert(
        DisneyParkFoodMenuContentAggregate $aggregate
    ): DisneyParkFoodMenuContentAggregate {
        $entity = $this->mstDisneyParkFoodMenuContentRepository->insert($aggregate->getEntity());
        return new DisneyParkFoodMenuContentAggregate($entity);
    }
}
