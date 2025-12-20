<?php

namespace App\Contexts\Application\Services\Talent;

use App\Contexts\Domain\Aggregates\Collection\SearchWordGroupAggregateList;
use App\Contexts\Domain\Aggregates\Collection\TalentSearchWordAggregateList;
use App\Repositories\MstSearchWordGroupRepository;
use App\Repositories\MstTalentSearchWordRepository;

class SearchWordApplicationService
{
    private $mstSearchWordGroupRepository;
    private $mstTalentSearchWordRepository;

    public function __construct(
        MstSearchWordGroupRepository $mstSearchWordGroupRepository,
        MstTalentSearchWordRepository $mstTalentSearchWordRepository
    ) {
        $this->mstSearchWordGroupRepository = $mstSearchWordGroupRepository;
        $this->mstTalentSearchWordRepository = $mstTalentSearchWordRepository;
    }

    public function selectSearchWordGroup(): SearchWordGroupAggregateList
    {
        return $this->mstSearchWordGroupRepository->all();
    }

    public function selectTalentSearchWord(): TalentSearchWordAggregateList
    {
        return $this->mstTalentSearchWordRepository->all();
    }

    public function selectTalentSearchWordByTalentId(string $talentId): TalentSearchWordAggregateList
    {
        return $this->mstTalentSearchWordRepository->getByTalentId($talentId);
    }

    public function findSearchWordGroupByIds(SearchWordGroupAggregateList $searchWordGroupAggregateList, array $searchWordGroupIds): SearchWordGroupAggregateList
    {
        // find
        $foundAggregateList = new SearchWordGroupAggregateList(new \Illuminate\Support\Collection());
        foreach ($searchWordGroupIds as $searchWordGroupId) {
            $searchWordGroupAggregate = $searchWordGroupAggregateList->firstById($searchWordGroupId);
            if ($searchWordGroupAggregate) {
                $foundAggregateList->add($searchWordGroupAggregate);
            }
        }
        return $foundAggregateList;
    }
}

