<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstTalentAccount;

class TalentAccountAggregate
{
    private $entity; // MstTalentAccount

    public function __construct(MstTalentAccount $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstTalentAccount
    {
        return $this->entity;
    }
}