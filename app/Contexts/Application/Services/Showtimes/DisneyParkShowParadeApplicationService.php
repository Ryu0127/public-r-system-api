<?php

namespace App\Contexts\Application\Services\Showtimes;

use App\Contexts\Domain\Aggregates\DisneyParkShowParadeAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkShowParadeAggregateList;
use App\Repositories\MstDisneyParkShowParadeRepository;

class DisneyParkShowParadeApplicationService
{
    private MstDisneyParkShowParadeRepository $mstDisneyParkShowParadeRepository;

    public function __construct(MstDisneyParkShowParadeRepository $mstDisneyParkShowParadeRepository)
    {
        $this->mstDisneyParkShowParadeRepository = $mstDisneyParkShowParadeRepository;
    }

    public function firstById($id): ?DisneyParkShowParadeAggregate
    {
        $entity = $this->mstDisneyParkShowParadeRepository->findPk($id);
        if ($entity === null) {
            return null;
        }
        return new DisneyParkShowParadeAggregate($entity);
    }

    public function selectAll(): DisneyParkShowParadeAggregateList
    {
        return $this->mstDisneyParkShowParadeRepository->all();
    }

    public function selectByParkType(int $parkType): DisneyParkShowParadeAggregateList
    {
        return $this->mstDisneyParkShowParadeRepository->findByParkType($parkType);
    }

    public function insert(DisneyParkShowParadeAggregate $aggregate): DisneyParkShowParadeAggregate
    {
        $entity = $this->mstDisneyParkShowParadeRepository->insert($aggregate->getEntity());
        return new DisneyParkShowParadeAggregate($entity);
    }
}
