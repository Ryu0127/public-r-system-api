<?php

namespace App\Contexts\Application\Services\Showtimes;

use App\Contexts\Domain\Aggregates\DisneyParkAttractionAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkAttractionAggregateList;
use App\Repositories\MstDisneyParkAttractionRepository;

class DisneyParkAttractionApplicationService
{
    private MstDisneyParkAttractionRepository $mstDisneyParkAttractionRepository;

    public function __construct(MstDisneyParkAttractionRepository $mstDisneyParkAttractionRepository)
    {
        $this->mstDisneyParkAttractionRepository = $mstDisneyParkAttractionRepository;
    }

    public function firstById($id): ?DisneyParkAttractionAggregate
    {
        $entity = $this->mstDisneyParkAttractionRepository->findPk($id);
        if ($entity === null) {
            return null;
        }
        return new DisneyParkAttractionAggregate($entity);
    }

    public function selectAll(): DisneyParkAttractionAggregateList
    {
        return $this->mstDisneyParkAttractionRepository->all();
    }

    public function selectByParkType(int $parkType): DisneyParkAttractionAggregateList
    {
        return $this->mstDisneyParkAttractionRepository->findByParkType($parkType);
    }

    public function insert(DisneyParkAttractionAggregate $aggregate): DisneyParkAttractionAggregate
    {
        $entity = $this->mstDisneyParkAttractionRepository->insert($aggregate->getEntity());
        return new DisneyParkAttractionAggregate($entity);
    }
}
