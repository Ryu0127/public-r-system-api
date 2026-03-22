<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\RelYoutubeMusicVideoTalent;

class RelYoutubeMusicVideoTalentAggregate
{
    private $entity; // RelYoutubeMusicVideoTalent

    public function __construct(RelYoutubeMusicVideoTalent $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): RelYoutubeMusicVideoTalent
    {
        return $this->entity;
    }
}