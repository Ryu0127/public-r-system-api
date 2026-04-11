<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\RelTalentGroupMember;

class RelTalentGroupMemberAggregate
{
    private $entity; // RelTalentGroupMember

    public function __construct(RelTalentGroupMember $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): RelTalentGroupMember
    {
        return $this->entity;
    }
}