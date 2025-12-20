<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstTalentSearchWord;

class TalentSearchWordAggregate
{
    private $entity; // MstTalentSearchWord

    public function __construct(MstTalentSearchWord $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstTalentSearchWord
    {
        return $this->entity;
    }
}

