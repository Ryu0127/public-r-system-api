<?php

namespace App\Contexts\Application\Services\Talent;

use App\Contexts\Domain\Aggregates\Collection\EventAggregateList;
use App\Contexts\Domain\Aggregates\Collection\EventCastTalentAggregateList;
use App\Contexts\Domain\Aggregates\Collection\EventTypeAggregateList;
use App\Contexts\Domain\Aggregates\Collection\TalentAggregateList;
use App\Contexts\Domain\Aggregates\EventAggregate;
use App\Contexts\Domain\Aggregates\EventCastTalentAggregate;
use App\Repositories\MstEventTypeRepository;
use App\Repositories\MstTalentRepository;
use App\Repositories\TblEventCastTalentRepository;
use App\Repositories\TblEventRepository;

class EventApplicationService
{
    private $tblEventRepository;
    private $mstEventTypeRepository;
    private $mstTalentRepository;
    private $tblEventCastTalentRepository;

    public function __construct(
        TblEventRepository $tblEventRepository,
        MstEventTypeRepository $mstEventTypeRepository,
        MstTalentRepository $mstTalentRepository,
        TblEventCastTalentRepository $tblEventCastTalentRepository,
    ) {
        $this->tblEventRepository = $tblEventRepository;
        $this->mstEventTypeRepository = $mstEventTypeRepository;
        $this->mstTalentRepository = $mstTalentRepository;
        $this->tblEventCastTalentRepository = $tblEventCastTalentRepository;
    }

    public function firstEventById(string $id): EventAggregate
    {
        return $this->tblEventRepository->findPk($id);
    }

    public function selectEvent(): EventAggregateList
    {
        return $this->tblEventRepository->all();
    }

    public function selectEventType(): EventTypeAggregateList
    {
        return $this->mstEventTypeRepository->all();
    }

    public function selectTalent(): TalentAggregateList
    {
        return $this->mstTalentRepository->all();
    }

    public function selectEventCastTalent(): EventCastTalentAggregateList
    {
        return $this->tblEventCastTalentRepository->all();
    }

    public function insertEvent(EventAggregate $eventAggregate): EventAggregate
    {
        $this->tblEventRepository->insert($eventAggregate->getEntity());
        return $eventAggregate;
    }

    public function insertEventCastTalent(EventCastTalentAggregate $eventCastTalentAggregate): EventCastTalentAggregate
    {
        $this->tblEventCastTalentRepository->insert($eventCastTalentAggregate->getEntity());
        return $eventCastTalentAggregate;
    }
}