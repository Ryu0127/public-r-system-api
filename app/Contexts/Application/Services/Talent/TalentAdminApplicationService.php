<?php

namespace App\Contexts\Application\Services\Talent;

use App\Contexts\Domain\Aggregates\Collection\TalentAggregateList;
use App\Contexts\Domain\Aggregates\Collection\TalentAccountAggregateList;
use App\Contexts\Domain\Aggregates\Collection\TalentHashtagAggregateList;
use App\Contexts\Domain\Aggregates\TalentAggregate;
use App\Contexts\Domain\Aggregates\TalentAccountAggregate;
use App\Contexts\Domain\Aggregates\TalentHashtagAggregate;
use App\Repositories\MstTalentRepository;
use App\Repositories\MstTalentAccountRepository;
use App\Repositories\MstTalentHashtagRepository;

class TalentAdminApplicationService
{
    private $mstTalentRepository;
    private $mstTalentAccountRepository;
    private $mstTalentHashtagRepository;

    public function __construct(
        MstTalentRepository $mstTalentRepository,
        MstTalentAccountRepository $mstTalentAccountRepository,
        MstTalentHashtagRepository $mstTalentHashtagRepository,
    ) {
        $this->mstTalentRepository = $mstTalentRepository;
        $this->mstTalentAccountRepository = $mstTalentAccountRepository;
        $this->mstTalentHashtagRepository = $mstTalentHashtagRepository;
    }

    /**
     * タレント全件取得
     */
    public function selectTalent(): TalentAggregateList
    {
        return $this->mstTalentRepository->all();
    }

    /**
     * タレントIDで1件取得
     */
    public function findTalentById(int $id): TalentAggregate
    {
        return $this->mstTalentRepository->findPk($id);
    }

    /**
     * タレントアカウント全件取得
     */
    public function selectTalentAccount(): TalentAccountAggregateList
    {
        return $this->mstTalentAccountRepository->all();
    }

    /**
     * タレントハッシュタグ全件取得
     */
    public function selectTalentHashtag(): TalentHashtagAggregateList
    {
        return $this->mstTalentHashtagRepository->all();
    }

    /**
     * タレント登録
     */
    public function insertTalent(TalentAggregate $talentAggregate): TalentAggregate
    {
        $entity = $this->mstTalentRepository->insert($talentAggregate->getEntity());
        return new TalentAggregate($entity);
    }

    /**
     * タレント更新
     */
    public function updateTalent(TalentAggregate $talentAggregate, int $id): TalentAggregate
    {
        $entity = $this->mstTalentRepository->updateByPk($talentAggregate->getEntity(), $id);
        return new TalentAggregate($entity);
    }

    /**
     * タレント削除
     */
    public function deleteTalent(int $id): TalentAggregate
    {
        $entity = $this->mstTalentRepository->deleteByPk($id);
        return new TalentAggregate($entity);
    }

    /**
     * タレントアカウント登録
     */
    public function insertTalentAccount(TalentAccountAggregate $talentAccountAggregate): TalentAccountAggregate
    {
        $entity = $this->mstTalentAccountRepository->insert($talentAccountAggregate->getEntity());
        return new TalentAccountAggregate($entity);
    }

    /**
     * タレントハッシュタグ登録
     */
    public function insertTalentHashtag(TalentHashtagAggregate $talentHashtagAggregate): TalentHashtagAggregate
    {
        $entity = $this->mstTalentHashtagRepository->insert($talentHashtagAggregate->getEntity());
        return new TalentHashtagAggregate($entity);
    }
}
