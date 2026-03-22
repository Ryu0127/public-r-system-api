<?php

namespace App\Contexts\Domain\Collection\Aggregates;

use App\Contexts\Domain\Aggregates\RelYoutubeMusicVideoTalentAggregate;
use Illuminate\Support\Collection;

class RelYoutubeMusicVideoTalentAggregateList
{
    private $aggregates; // Collection

    public function __construct(Collection $aggregates)
    {
        $this->aggregates = $aggregates;
    }

    public function getAggregates(): Collection
    {
        return $this->aggregates;
    }

    public function getYoutubeMusicVideoIds(): array
    {
        return $this->aggregates->map(fn ($a) => $a->getEntity()->youtube_music_video_id)->values()->all();
    }

    public function filterByTalentIds(array $talentIds): RelYoutubeMusicVideoTalentAggregateList
    {
        return new RelYoutubeMusicVideoTalentAggregateList($this->aggregates->filter(function ($aggregate) use ($talentIds) {
            return in_array($aggregate->getEntity()->talent_id, $talentIds);
        }));
    }

    public function firstById(string $id): ?RelYoutubeMusicVideoTalentAggregate
    {
        return $this->aggregates->first(function ($aggregate) use ($id) {
            return $aggregate->getEntity()->id == $id;
        });
    }

    public function add(RelYoutubeMusicVideoTalentAggregate $aggregate)
    {
        $this->aggregates->add($aggregate);
    }
}