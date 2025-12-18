<?php

namespace App\Contexts\Application\Services\Talent;

use App\Contexts\Domain\Aggregates\Collection\TalentAccountAggregateList;
use App\Contexts\Domain\Aggregates\Collection\TalentAggregateList;
use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Repositories\MstTalentAccountRepository;
use App\Repositories\MstTalentRepository;
use Illuminate\Support\Collection;

class TalentAccountApplicationService
{
    private $mstTalentRepository;
    private $mstTalentAccountRepository;

    public function __construct(
        MstTalentRepository $mstTalentRepository,
        MstTalentAccountRepository $mstTalentAccountRepository
    ) {
        $this->mstTalentRepository = $mstTalentRepository;
        $this->mstTalentAccountRepository = $mstTalentAccountRepository;
    }

    public function findTalent(string $talentId): TalentAggregate
    {
        return $this->mstTalentRepository->findPk($talentId);
    }

    public function selectTalent(): TalentAggregateList
    {
        return $this->mstTalentRepository->all();
    }

    public function selectTalentAccount(): TalentAccountAggregateList
    {
        return $this->mstTalentAccountRepository->all();
    }

    public function findTalentAccountByTalentId(TalentAccountAggregateList $talentAccountAggregateList, string $talentId): TalentAccountAggregateList
    {
        // target
        if(!in_array($talentId, $talentAccountAggregateList->getTalentIds())) return new TalentAccountAggregateList(new Collection());
        // find
        return $talentAccountAggregateList->filterByTalentId([$talentId]);
    }
}