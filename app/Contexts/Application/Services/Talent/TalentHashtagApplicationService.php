<?php

namespace App\Contexts\Application\Services\Talent;

use App\Contexts\Domain\Aggregates\Collection\TalentAggregateList;
use App\Contexts\Domain\Aggregates\Collection\TalentHashtagAggregateList;
use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Repositories\MstTalentRepository;
use App\Repositories\MstTalentHashtagRepository;
use Illuminate\Support\Collection;

class TalentHashtagApplicationService
{
    private $mstTalentRepository;
    private $mstTalentHashtagRepository;

    public function __construct(
        MstTalentRepository $mstTalentRepository,
        MstTalentHashtagRepository $mstTalentHashtagRepository
    ) {
        $this->mstTalentRepository = $mstTalentRepository;
        $this->mstTalentHashtagRepository = $mstTalentHashtagRepository;
    }

    public function findTalent(string $talentId): TalentAggregate
    {
        return $this->mstTalentRepository->findPk($talentId);
    }

    public function selectTalent(): TalentAggregateList
    {
        return $this->mstTalentRepository->all();
    }

    public function selectTalentHashtag(): TalentHashtagAggregateList
    {
        return $this->mstTalentHashtagRepository->all();
    }

    public function selectTalentHashtagByTalentId(string $talentId): TalentHashtagAggregateList
    {
        return $this->mstTalentHashtagRepository->getByTalentId($talentId);
    }

    public function findTalentByTalentHashtag(TalentAggregateList $talentAggregateList, TalentHashtagAggregateList $talentHashtagAggregateList): TalentAggregateList
    {
        // target
        $talentIds = $talentHashtagAggregateList->getTalentIds();
        // find
        $foundAggregateList = new TalentAggregateList(new Collection());
        foreach ($talentAggregateList->getAggregates() as $talentAggregate) {
            $talentEntity = $talentAggregate->getEntity();
            if(!in_array($talentEntity->id, $talentIds)) continue;

            $foundAggregateList->add($talentAggregate);
        }
        return $foundAggregateList;
    }
}