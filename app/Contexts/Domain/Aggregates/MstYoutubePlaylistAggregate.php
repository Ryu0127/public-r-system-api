<?php

namespace App\Contexts\Domain\Aggregates;

use App\Models\MstYoutubePlaylist;

class MstYoutubePlaylistAggregate
{
    private const YOUTUBE_PLAYLIST_URL = 'https://www.youtube.com/playlist?list=';

    private $entity; // MstYoutubePlaylist

    public function __construct(MstYoutubePlaylist $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): MstYoutubePlaylist
    {
        return $this->entity;
    }

    /**
     * YouTube プレイリストの URL
     */
    public function getPlaylistUrl(): string
    {
        return self::YOUTUBE_PLAYLIST_URL . rawurlencode((string) $this->entity->youtube_playlist_code);
    }
}
