<?php

namespace App\Apis\Admin;

use App\Http\Controllers\Controller;
use App\Models\MstTalent;
use App\Models\MstYoutubeMusicVideo;
use App\Models\MstYoutubePlaylist;
use App\Models\RelYoutubeMusicVideoTalent;
use App\Models\RelYoutubePlaylistMusicVideo;
use App\Models\RelYoutubePlaylistMusicVideoTalent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * プレイリスト管理 - プレイリストへの楽曲紐付け（楽曲ごとのタレント紐付けを含む）
 */
class MusicPlaylistMusicController extends Controller
{
    private const PROGRAM_NAME = 'admin-api';
    /** 1プレイリストに紐付けできる楽曲数の上限 */
    private const MAX_MUSIC_COUNT = 500;
    /** 1楽曲に紐付けできるタレント数の上限 */
    private const MAX_TALENT_COUNT_PER_MUSIC = 50;

    /**
     * 紐付け楽曲一覧取得API（並び順）
     * GET /admin/music-playlists/{id}/music
     *
     * 削除済み（view_flag=0）の楽曲は返さない
     */
    public function index(string $id): JsonResponse
    {
        $playlist = MstYoutubePlaylist::query()->find($id);
        if (!$playlist) {
            return $this->notFoundResponse();
        }

        return response()->json([
            'success' => true,
            'data' => $this->buildMusicList((int) $playlist->id),
            'message' => '紐付け楽曲を取得しました',
        ]);
    }

    /**
     * 紐付け楽曲更新API（指定された楽曲・順序・タレントで置き換え）
     * PUT /admin/music-playlists/{id}/music
     *
     * Body: { "items": [ { "musicId": 楽曲ID, "talentIds": [タレントID, ...] }, ... ] }
     * ※配列の順序がそのまま並び順になる。空配列で全解除
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $playlist = MstYoutubePlaylist::query()->find($id);
        if (!$playlist) {
            return $this->notFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'items' => ['present', 'array', 'max:' . self::MAX_MUSIC_COUNT],
            'items.*.musicId' => ['required', 'integer', 'distinct'],
            'items.*.talentIds' => ['present', 'array', 'max:' . self::MAX_TALENT_COUNT_PER_MUSIC],
            'items.*.talentIds.*' => ['integer'],
        ], [
            'items.present' => '楽曲の指定がありません',
            'items.array' => '楽曲の指定形式が正しくありません',
            'items.max' => '紐付けできる楽曲は' . self::MAX_MUSIC_COUNT . '曲までです',
            'items.*.musicId.required' => '楽曲IDの指定がありません',
            'items.*.musicId.integer' => '楽曲IDの形式が正しくありません',
            'items.*.musicId.distinct' => '同じ楽曲が重複して指定されています',
            'items.*.talentIds.present' => 'タレントの指定がありません',
            'items.*.talentIds.array' => 'タレントの指定形式が正しくありません',
            'items.*.talentIds.max' => '1曲に紐付けできるタレントは' . self::MAX_TALENT_COUNT_PER_MUSIC . '名までです',
            'items.*.talentIds.*.integer' => 'タレントIDの形式が正しくありません',
        ]);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $items = $this->normalizeItems($request->input('items', []));
        $musicIds = array_column($items, 'musicId');

        // 存在しない・削除済みの楽曲が含まれていないか確認
        $existingMusicIds = MstYoutubeMusicVideo::query()
            ->whereIn('id', $musicIds)
            ->where('view_flag', 1)
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->all();
        $missingMusicIds = array_values(array_diff($musicIds, $existingMusicIds));
        if (count($missingMusicIds) > 0) {
            return $this->validationErrorResponse([
                'items' => ['存在しない、または削除済みの楽曲が含まれています（ID: ' . implode(', ', $missingMusicIds) . '）'],
            ]);
        }

        // 存在しないタレントが含まれていないか確認
        $talentIds = array_values(array_unique(array_merge([], ...array_column($items, 'talentIds'))));
        $existingTalentIds = MstTalent::query()
            ->whereIn('id', $talentIds)
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->all();
        $missingTalentIds = array_values(array_diff($talentIds, $existingTalentIds));
        if (count($missingTalentIds) > 0) {
            return $this->validationErrorResponse([
                'items' => ['存在しないタレントが含まれています（ID: ' . implode(', ', $missingTalentIds) . '）'],
            ]);
        }

        DB::beginTransaction();
        try {
            $playlistId = (int) $playlist->id;
            RelYoutubePlaylistMusicVideo::query()->where('youtube_playlist_id', $playlistId)->delete();
            RelYoutubePlaylistMusicVideoTalent::query()->where('youtube_playlist_id', $playlistId)->delete();

            $now = now();
            foreach ($items as $index => $item) {
                $rel = new RelYoutubePlaylistMusicVideo();
                $rel->youtube_playlist_id = $playlistId;
                $rel->youtube_music_video_id = $item['musicId'];
                $rel->sort = $index + 1;
                $this->stampCreated($rel, $now);
                $rel->save();

                foreach ($item['talentIds'] as $talentId) {
                    $talentRel = new RelYoutubePlaylistMusicVideoTalent();
                    $talentRel->youtube_playlist_id = $playlistId;
                    $talentRel->youtube_music_video_id = $item['musicId'];
                    $talentRel->talent_id = $talentId;
                    $this->stampCreated($talentRel, $now);
                    $talentRel->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $this->buildMusicList($playlistId),
                'message' => '紐付け楽曲を保存しました',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MusicPlaylistMusicController@update failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'items' => $items,
            ]);
            return response()->json([
                'success' => false,
                'message' => '紐付け楽曲の保存に失敗しました',
            ], 500);
        }
    }

    /**
     * リクエストの items を正規化（ID を整数化し、タレントIDの重複を除去）
     * @return array<int, array{musicId: int, talentIds: int[]}>
     */
    private function normalizeItems(array $rawItems): array
    {
        return array_values(array_map(function ($item) {
            $talentIds = array_map('intval', $item['talentIds'] ?? []);
            return [
                'musicId' => (int) $item['musicId'],
                'talentIds' => array_values(array_unique($talentIds)),
            ];
        }, $rawItems));
    }

    private function stampCreated($model, $now): void
    {
        $model->created_datetime = $now;
        $model->created_program_name = self::PROGRAM_NAME;
        $model->updated_datetime = $now;
        $model->updated_program_name = self::PROGRAM_NAME;
    }

    /**
     * プレイリストの紐付け楽曲（並び順 → ID順）
     * - talentIds: プレイリスト上でこの楽曲に紐付けたタレント
     * - musicTalentIds: 楽曲管理で楽曲に紐付いているタレント（反映用の参考情報）
     */
    private function buildMusicList(int $playlistId): array
    {
        $rels = RelYoutubePlaylistMusicVideo::query()
            ->where('youtube_playlist_id', $playlistId)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
        $musicIds = $rels->pluck('youtube_music_video_id')->filter()->map(fn ($v) => (int) $v)->all();

        $musicById = MstYoutubeMusicVideo::query()
            ->whereIn('id', $musicIds)
            ->where('view_flag', 1)
            ->get()
            ->keyBy(fn ($music) => (int) $music->id);

        $playlistTalentIdsByMusic = $this->groupTalentIdsByMusic(
            RelYoutubePlaylistMusicVideoTalent::query()
                ->where('youtube_playlist_id', $playlistId)
                ->orderBy('id')
                ->get()
        );
        $musicTalentIdsByMusic = $this->groupTalentIdsByMusic(
            RelYoutubeMusicVideoTalent::query()
                ->whereIn('youtube_music_video_id', $musicIds)
                ->orderBy('id')
                ->get()
        );

        return $rels
            ->filter(fn ($rel) => $musicById->has((int) $rel->youtube_music_video_id))
            ->map(function ($rel) use ($musicById, $playlistTalentIdsByMusic, $musicTalentIdsByMusic) {
                $musicId = (int) $rel->youtube_music_video_id;
                $music = $musicById->get($musicId);
                return [
                    'musicId' => $musicId,
                    'title' => (string) ($music->music_title ?? ''),
                    'youtubeVideoId' => (string) ($music->youtube_video_code ?? ''),
                    'type' => ((string) $music->music_type === '2') ? 'cover' : 'original',
                    'releaseDate' => $music->public_date ? substr((string) $music->public_date, 0, 10) : '',
                    'sort' => (int) ($rel->sort ?? 0),
                    'talentIds' => $playlistTalentIdsByMusic[$musicId] ?? [],
                    'musicTalentIds' => $musicTalentIdsByMusic[$musicId] ?? [],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, int[]> 楽曲ID → タレントID一覧（重複除去・取得順）
     */
    private function groupTalentIdsByMusic($rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            $musicId = (int) $row->youtube_music_video_id;
            $talentId = (int) $row->talent_id;
            if (!in_array($talentId, $map[$musicId] ?? [], true)) {
                $map[$musicId][] = $talentId;
            }
        }
        return $map;
    }

    private function validationErrorResponse(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'バリデーションエラーが発生しました',
            'errors' => $errors,
        ], 422);
    }

    private function notFoundResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'プレイリストが見つかりませんでした',
        ], 404);
    }
}
