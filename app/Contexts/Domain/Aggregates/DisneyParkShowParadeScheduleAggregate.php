<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\TblDisneyParkShowParadeSchedule;

class DisneyParkShowParadeScheduleAggregate
{
    private $entity; // TblDisneyParkShowParadeSchedule

    public function __construct(TblDisneyParkShowParadeSchedule $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): TblDisneyParkShowParadeSchedule
    {
        return $this->entity;
    }
}
