<?php

namespace App\Contexts\Application\Services\Showtimes;

use App\Contexts\Domain\Aggregates\DisneyParkShowParadeScheduleAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkShowParadeScheduleAggregateList;
use App\Repositories\TblDisneyParkShowParadeScheduleRepository;

class DisneyParkShowParadeScheduleApplicationService
{
    private TblDisneyParkShowParadeScheduleRepository $tblDisneyParkShowParadeScheduleRepository;

    public function __construct(
        TblDisneyParkShowParadeScheduleRepository $tblDisneyParkShowParadeScheduleRepository
    ) {
        $this->tblDisneyParkShowParadeScheduleRepository = $tblDisneyParkShowParadeScheduleRepository;
    }

    public function firstById($id): ?DisneyParkShowParadeScheduleAggregate
    {
        $entity = $this->tblDisneyParkShowParadeScheduleRepository->findPk($id);
        if ($entity === null) {
            return null;
        }
        return new DisneyParkShowParadeScheduleAggregate($entity);
    }

    public function selectAll(): DisneyParkShowParadeScheduleAggregateList
    {
        return $this->tblDisneyParkShowParadeScheduleRepository->all();
    }

    public function selectByShowParadeId(int $showParadeId): DisneyParkShowParadeScheduleAggregateList
    {
        return $this->tblDisneyParkShowParadeScheduleRepository->findByShowParadeId($showParadeId);
    }

    /**
     * @param list<int> $showParadeIds
     */
    public function selectByShowParadeIds(array $showParadeIds): DisneyParkShowParadeScheduleAggregateList
    {
        return $this->tblDisneyParkShowParadeScheduleRepository->findByShowParadeIds($showParadeIds);
    }

    public function insert(
        DisneyParkShowParadeScheduleAggregate $aggregate
    ): DisneyParkShowParadeScheduleAggregate {
        $entity = $this->tblDisneyParkShowParadeScheduleRepository->insert($aggregate->getEntity());
        return new DisneyParkShowParadeScheduleAggregate($entity);
    }
}
