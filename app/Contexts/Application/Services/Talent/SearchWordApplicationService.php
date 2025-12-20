<?php

namespace App\Contexts\Application\Services\Talent;

use App\Contexts\Domain\Aggregates\Collection\SearchWordGroupAggregateList;
use App\Contexts\Domain\Aggregates\Collection\TalentSearchWordAggregateList;
use App\Contexts\Domain\Aggregates\TalentSearchWordAggregate;
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

    /**
     * タレントIDで検索ワードを削除
     * @param string $talentId
     */
    public function deleteTalentSearchWordByTalentId(string $talentId): void
    {
        $this->mstTalentSearchWordRepository->deleteByTalentId($talentId);
    }

    /**
     * 検索ワードを登録
     * @param TalentSearchWordAggregate $talentSearchWordAggregate
     * @return TalentSearchWordAggregate
     */
    public function insertTalentSearchWord(TalentSearchWordAggregate $talentSearchWordAggregate): TalentSearchWordAggregate
    {
        $entity = $this->mstTalentSearchWordRepository->insert($talentSearchWordAggregate->getEntity());
        return new TalentSearchWordAggregate($entity);
    }

    /**
     * 検索ワードグループを名前で検索、存在しない場合は作成
     * @param string $groupName
     * @return int 検索ワードグループID
     */
    public function findOrCreateSearchWordGroupByName(string $groupName): int
    {
        $searchWordGroupAggregateList = $this->selectSearchWordGroup();
        $existingGroup = $searchWordGroupAggregateList->getAggregates()->first(function ($aggregate) use ($groupName) {
            return $aggregate->getEntity()->search_word_group_name === $groupName;
        });

        if ($existingGroup) {
            return $existingGroup->getEntity()->id;
        }

        // 存在しない場合は新規作成
        $newGroupEntity = new \App\Models\MstSearchWordGroup();
        $newGroupEntity->search_word_group_name = $groupName;
        $newGroupEntity->created_program_name = 'admin-api';
        $newGroupEntity->updated_program_name = 'admin-api';
        $newGroup = $this->mstSearchWordGroupRepository->insert($newGroupEntity);
        return $newGroup->id;
    }
}

