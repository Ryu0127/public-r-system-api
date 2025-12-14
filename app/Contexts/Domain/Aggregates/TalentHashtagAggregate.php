<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstTalentHashtag;

class TalentHashtagAggregate
{
    private $entity; // MstTalentHashtag

    public function __construct(MstTalentHashtag $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstTalentHashtag
    {
        return $this->entity;
    }
}