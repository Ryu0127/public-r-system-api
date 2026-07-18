<?php

namespace App\Contexts\Application\Services\Showtimes;

use App\Contexts\Domain\Aggregates\DisneyParkAttractionWaitForecastAggregate;
use App\Contexts\Domain\Collection\Aggregates\DisneyParkAttractionWaitForecastAggregateList;
use App\Repositories\TblDisneyParkAttractionWaitForecastRepository;

class DisneyParkAttractionWaitForecastApplicationService
{
    private TblDisneyParkAttractionWaitForecastRepository $tblDisneyParkAttractionWaitForecastRepository;

    public function __construct(
        TblDisneyParkAttractionWaitForecastRepository $tblDisneyParkAttractionWaitForecastRepository
    ) {
        $this->tblDisneyParkAttractionWaitForecastRepository = $tblDisneyParkAttractionWaitForecastRepository;
    }

    public function firstById($id): ?DisneyParkAttractionWaitForecastAggregate
    {
        $entity = $this->tblDisneyParkAttractionWaitForecastRepository->findPk($id);
        if ($entity === null) {
            return null;
        }
        return new DisneyParkAttractionWaitForecastAggregate($entity);
    }

    public function selectAll(): DisneyParkAttractionWaitForecastAggregateList
    {
        return $this->tblDisneyParkAttractionWaitForecastRepository->all();
    }

    public function selectByTargetDate(string $targetDate): DisneyParkAttractionWaitForecastAggregateList
    {
        return $this->tblDisneyParkAttractionWaitForecastRepository->findByTargetDate($targetDate);
    }

    /**
     * @param list<int> $disneyParkAttractionIds
     */
    public function selectByDisneyParkAttractionIdsAndTargetDate(
        array $disneyParkAttractionIds,
        string $targetDate
    ): DisneyParkAttractionWaitForecastAggregateList {
        return $this->tblDisneyParkAttractionWaitForecastRepository
            ->findByDisneyParkAttractionIdsAndTargetDate($disneyParkAttractionIds, $targetDate);
    }

    public function insert(
        DisneyParkAttractionWaitForecastAggregate $aggregate
    ): DisneyParkAttractionWaitForecastAggregate {
        $entity = $this->tblDisneyParkAttractionWaitForecastRepository->insert($aggregate->getEntity());
        return new DisneyParkAttractionWaitForecastAggregate($entity);
    }
}
