<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstYoutubeMusicVideo;

class MstYoutubeMusicVideoAggregate
{
    private $entity; // MstYoutubeMusicVideo

    public function __construct(MstYoutubeMusicVideo $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstYoutubeMusicVideo
    {
        return $this->entity;
    }
}