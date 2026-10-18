<?php

namespace App\Repositories;

use App\Contexts\Domain\Aggregates\MstYoutubePlaylistAggregate;
use App\Contexts\Domain\Collection\Aggregates\MstYoutubePlaylistAggregateList;
use App\Models\MstYoutubePlaylist;
use Illuminate\Support\Collection;

class MstYoutubePlaylistRepository
{
    /**
     * 複数件取得（全件）
     */
    public function all(): MstYoutubePlaylistAggregateList
    {
        return $this->createAggregateList(MstYoutubePlaylist::get());
    }

    private function createAggregateList(Collection $entities): MstYoutubePlaylistAggregateList
    {
        return new MstYoutubePlaylistAggregateList(new Collection($entities->map(function ($entity) {
            return new MstYoutubePlaylistAggregate($entity);
        })));
    }
}
