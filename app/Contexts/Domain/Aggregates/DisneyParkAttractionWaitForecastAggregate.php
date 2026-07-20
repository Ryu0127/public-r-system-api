<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\TblDisneyParkAttractionWaitForecast;

class DisneyParkAttractionWaitForecastAggregate
{
    private $entity; // TblDisneyParkAttractionWaitForecast

    public function __construct(TblDisneyParkAttractionWaitForecast $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): TblDisneyParkAttractionWaitForecast
    {
        return $this->entity;
    }
}
