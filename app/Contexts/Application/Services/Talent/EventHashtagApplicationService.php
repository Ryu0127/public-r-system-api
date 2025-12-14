<?php

namespace App\Contexts\Application\Services\Talent;

use App\Contexts\Domain\Aggregates\Collection\EventAggregateList;
use App\Contexts\Domain\Aggregates\Collection\EventCastTalentAggregateList;
use App\Contexts\Domain\Aggregates\Collection\EventHashtagAggregateList;
use App\Contexts\Domain\Aggregates\Collection\EventTypeAggregateList;
use App\Repositories\MstEventTypeRepository;
use App\Repositories\MstTalentRepository;
use App\Repositories\TblEventCastTalentRepository;
use App\Repositories\TblEventHashtagRepository;
use App\Repositories\TblEventRepository;
use Carbon\Carbon;

class EventHashtagApplicationService
{
    private $mstEventTypeRepository;
    private $mstTalentRepository;
    private $tblEventCastTalentRepository;
    private $tblEventRepository;
    private $tblEventHashtagRepository;

    public function __construct(
        MstEventTypeRepository $mstEventTypeRepository,
        MstTalentRepository $mstTalentRepository,
        TblEventCastTalentRepository $tblEventCastTalentRepository,
        TblEventRepository $tblEventRepository,
        TblEventHashtagRepository $tblEventHashtagRepository,
    ) {
        $this->mstEventTypeRepository = $mstEventTypeRepository;
        $this->mstTalentRepository = $mstTalentRepository;
        $this->tblEventCastTalentRepository = $tblEventCastTalentRepository;
        $this->tblEventRepository = $tblEventRepository;
        $this->tblEventHashtagRepository = $tblEventHashtagRepository;
    }

    public function selectEventType(): EventTypeAggregateList
    {
        return $this->mstEventTypeRepository->all();
    }

    public function selectEventCastTalentByTalentId(string $talentId): EventCastTalentAggregateList
    {
        return $this->tblEventCastTalentRepository->getByTalentId($talentId);
    }

    public function selectEventByIds(array $ids): EventAggregateList
    {
        return $this->tblEventRepository->getByPkIds($ids);
    }

    public function selectEventHashtagByEventIds(array $ids): EventHashtagAggregateList
    {
        return $this->tblEventHashtagRepository->getByEventIds($ids);
    }

    public function findEventAggregateListByEventHashtags(EventAggregateList $eventAggregateList, EventHashtagAggregateList $eventHashtagAggregateList): EventAggregateList
    {
        // target
        $eventIds = $eventHashtagAggregateList->getEventIds();
        // find
        $findAggregates = $eventAggregateList->getAggregates()->filter(function ($eventAggregate) use ($eventIds) {
            return in_array($eventAggregate->getEntity()->id, $eventIds);
        });
        return new EventAggregateList($findAggregates);
    }

    public function findEventAggregateListByNotEndDateOver(EventAggregateList $eventAggregateList, $reserveDays): EventAggregateList
    {
        // find
        $findAggregates = $eventAggregateList->getAggregates()->filter(function ($eventAggregate) use ($reserveDays) {
            if(empty($eventAggregate->getEntity()->event_end_date)) return true;
            $endDate = Carbon::parse($eventAggregate->getEntity()->event_end_date)->addDays($reserveDays);
            return $endDate->isAfter(Carbon::now());
        });
        return new EventAggregateList($findAggregates);
    }
}