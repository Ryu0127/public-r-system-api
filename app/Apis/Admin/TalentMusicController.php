<?php

namespace App\Apis\Admin;

use App\Http\Controllers\Controller;
use App\Models\MstYoutubeMusicVideo;
use App\Models\RelYoutubeMusicVideoTalent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TalentMusicController extends Controller
{
    /**
     * 楽曲一覧取得API
     * GET /admin/talent-music
     */
    public function index(): JsonResponse
    {
        $musicList = MstYoutubeMusicVideo::query()
            ->where('view_flag', 1)
            ->orderByDesc('id')
            ->get();

        $musicIds = $musicList->pluck('id')->all();
        $talentMap = RelYoutubeMusicVideoTalent::query()
            ->whereIn('youtube_music_video_id', $musicIds)
            ->get()
            ->groupBy('youtube_music_video_id')
            ->map(fn ($rows) => $rows->pluck('talent_id')->map(fn ($id) => (int) $id)->values()->all());

        return response()->json([
            'success' => true,
            'data' => $musicList->map(function (MstYoutubeMusicVideo $music) use ($talentMap) {
                return $this->toResponseRow($music, $talentMap[(int) $music->id] ?? []);
            })->values(),
            'message' => '楽曲一覧を取得しました',
        ]);
    }

    /**
     * 楽曲詳細取得API
     * GET /admin/talent-music/{id}
     */
    public function show(string $id): JsonResponse
    {
        $music = MstYoutubeMusicVideo::query()
            ->where('view_flag', 1)
            ->find($id);
        if (!$music) {
            return response()->json([
                'success' => false,
                'message' => '楽曲が見つかりませんでした',
            ], 404);
        }

        $talentIds = RelYoutubeMusicVideoTalent::query()
            ->where('youtube_music_video_id', $music->id)
            ->pluck('talent_id')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => $this->toResponseRow($music, $talentIds),
            'message' => '楽曲詳細を取得しました',
        ]);
    }

    /**
     * 楽曲登録API
     * POST /admin/talent-music
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'talentIds' => 'required|array|min:1',
            'talentIds.*' => 'integer',
            'youtubeVideoId' => 'required|string|max:255',
            'type' => 'required|in:original,cover',
            'releaseDate' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'バリデーションエラーが発生しました',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $music = new MstYoutubeMusicVideo();
            $music->music_title = $request->input('title');
            $music->youtube_video_code = $request->input('youtubeVideoId');
            $music->public_date = $request->input('releaseDate');
            // DB側は music_type が '1'(original) / '2'(cover) 前提
            $music->music_type = $this->toDbType($request->input('type'));
            $music->view_flag = 1;
            $music->created_program_name = 'admin-api';
            $music->updated_program_name = 'admin-api';
            // created_datetime / updated_datetime が DB制約上必須の場合に備えてセット
            $music->created_datetime = now();
            $music->updated_datetime = now();
            $music->save();

            foreach ($request->input('talentIds', []) as $talentId) {
                $rel = new RelYoutubeMusicVideoTalent();
                $rel->youtube_music_video_id = $music->id;
                $rel->talent_id = (int) $talentId;
                $rel->created_program_name = 'admin-api';
                $rel->updated_program_name = 'admin-api';
                $rel->created_datetime = now();
                $rel->updated_datetime = now();
                $rel->save();
            }

            DB::commit();

            $talentIds = RelYoutubeMusicVideoTalent::query()
                ->where('youtube_music_video_id', $music->id)
                ->pluck('talent_id')
                ->map(fn ($v) => (int) $v)
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'message' => '楽曲を登録しました',
                'data' => $this->toResponseRow($music->fresh(), $talentIds),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TalentMusicController@store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => '楽曲の登録に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 楽曲更新API
     * PUT /admin/talent-music/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'talentIds' => 'required|array|min:1',
            'talentIds.*' => 'integer',
            'youtubeVideoId' => 'required|string|max:255',
            'type' => 'required|in:original,cover',
            'releaseDate' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'バリデーションエラーが発生しました',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $music = MstYoutubeMusicVideo::query()->find($id);
            if (!$music) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => '楽曲が見つかりませんでした',
                ], 404);
            }

            $music->music_title = $request->input('title');
            $music->youtube_video_code = $request->input('youtubeVideoId');
            $music->public_date = $request->input('releaseDate');
            // DB側は music_type が '1'(original) / '2'(cover) 前提
            $music->music_type = $this->toDbType($request->input('type'));
            $music->updated_program_name = 'admin-api';
            $music->updated_datetime = now();
            $music->save();

            RelYoutubeMusicVideoTalent::query()
                ->where('youtube_music_video_id', $music->id)
                ->delete();

            foreach ($request->input('talentIds', []) as $talentId) {
                $rel = new RelYoutubeMusicVideoTalent();
                $rel->youtube_music_video_id = $music->id;
                $rel->talent_id = (int) $talentId;
                $rel->created_program_name = 'admin-api';
                $rel->updated_program_name = 'admin-api';
                $rel->created_datetime = now();
                $rel->updated_datetime = now();
                $rel->save();
            }

            DB::commit();

            $talentIds = RelYoutubeMusicVideoTalent::query()
                ->where('youtube_music_video_id', $music->id)
                ->pluck('talent_id')
                ->map(fn ($v) => (int) $v)
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'message' => '楽曲を更新しました',
                'data' => $this->toResponseRow($music->fresh(), $talentIds),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TalentMusicController@update failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => '楽曲の更新に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 楽曲削除API
     * DELETE /admin/talent-music/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $music = MstYoutubeMusicVideo::query()->find($id);
            if (!$music) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => '楽曲が見つかりませんでした',
                ], 404);
            }

            // 削除は物理削除せず soft delete（view_flag=0）
            $music->view_flag = 0;
            $music->updated_program_name = 'admin-api';
            $music->updated_datetime = now();
            $music->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '楽曲を削除しました',
                'data' => [
                    'id' => (int) $id,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TalentMusicController@destroy failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => '楽曲の削除に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function toResponseRow(MstYoutubeMusicVideo $music, array $talentIds): array
    {
        return [
            'id' => (int) $music->id,
            'title' => (string) ($music->music_title ?? ''),
            'talentIds' => array_values(array_map(fn ($id) => (int) $id, $talentIds)),
            'youtubeVideoId' => (string) ($music->youtube_video_code ?? ''),
            'type' => $this->toResponseType($music->music_type),
            'releaseDate' => $this->normalizeDate($music->public_datetime ?? $music->public_date ?? ''),
            'description' => '',
            'createdAt' => $music->created_datetime ?? null,
            'updatedAt' => $music->updated_datetime ?? null,
        ];
    }

    private function toResponseType($rawType): string
    {
        // DB側: '1' => original / '2' => cover
        if ($rawType === 'cover') {
            return 'cover';
        }
        if ($rawType === '2' || $rawType === 2) {
            return 'cover';
        }
        return 'original';
    }

    private function toDbType(string $type): string
    {
        // Frontend: original|cover
        return $type === 'cover' ? '2' : '1';
    }

    private function normalizeDate($rawDate): string
    {
        if (!$rawDate) {
            return '';
        }
        return substr((string) $rawDate, 0, 10);
    }
}
