<?php

namespace App\Apis\Admin;

use App\Http\Controllers\Controller;
use App\Models\MstYoutubePlaylist;
use App\Models\RelYoutubePlaylistMusicVideo;
use App\Models\RelYoutubePlaylistMusicVideoTalent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * プレイリスト管理（楽曲一覧画面に表示する YouTube プレイリスト）
 */
class MusicPlaylistsController extends Controller
{
    private const PROGRAM_NAME = 'admin-api';
    private const YOUTUBE_PLAYLIST_URL = 'https://www.youtube.com/playlist?list=';
    private const PLAYLIST_CODE_PATTERN = '/^[A-Za-z0-9_-]{2,64}$/';

    /**
     * プレイリスト一覧取得API（非表示も含む）
     * GET /admin/music-playlists
     */
    public function index(): JsonResponse
    {
        $playlists = MstYoutubePlaylist::query()
            ->orderBy('id')
            ->get();
        $musicCounts = $this->countMusicByPlaylistIds($playlists->pluck('id')->all());

        return response()->json([
            'success' => true,
            'data' => $playlists->map(function (MstYoutubePlaylist $playlist) use ($musicCounts) {
                return $this->toResponseRow($playlist, $musicCounts[(int) $playlist->id] ?? 0);
            })->values(),
            'message' => 'プレイリスト一覧を取得しました',
        ]);
    }

    /**
     * プレイリスト詳細取得API
     * GET /admin/music-playlists/{id}
     */
    public function show(string $id): JsonResponse
    {
        $playlist = MstYoutubePlaylist::query()->find($id);
        if (!$playlist) {
            return $this->notFoundResponse();
        }

        return response()->json([
            'success' => true,
            'data' => $this->toResponseRow($playlist),
            'message' => 'プレイリスト詳細を取得しました',
        ]);
    }

    /**
     * プレイリスト登録API
     * POST /admin/music-playlists
     */
    public function store(Request $request): JsonResponse
    {
        $input = $this->normalizeInput($request);
        $validator = Validator::make($input, $this->rules(null), $this->messages());
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $now = now();
            $playlist = new MstYoutubePlaylist();
            $this->fill($playlist, $input);
            $playlist->created_datetime = $now;
            $playlist->created_program_name = self::PROGRAM_NAME;
            $playlist->updated_datetime = $now;
            $playlist->updated_program_name = self::PROGRAM_NAME;
            $playlist->save();

            return response()->json([
                'success' => true,
                'message' => 'プレイリストを登録しました',
                'data' => $this->toResponseRow($playlist->fresh()),
            ], 201);
        } catch (\Exception $e) {
            Log::error('MusicPlaylistsController@store failed', [
                'error' => $e->getMessage(),
                'payload' => $input,
            ]);
            return $this->serverErrorResponse('プレイリストの登録に失敗しました');
        }
    }

    /**
     * プレイリスト更新API
     * PUT /admin/music-playlists/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $playlist = MstYoutubePlaylist::query()->find($id);
        if (!$playlist) {
            return $this->notFoundResponse();
        }

        $input = $this->normalizeInput($request);
        $validator = Validator::make($input, $this->rules((int) $playlist->id), $this->messages());
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $this->fill($playlist, $input);
            $playlist->updated_datetime = now();
            $playlist->updated_program_name = self::PROGRAM_NAME;
            $playlist->save();

            return response()->json([
                'success' => true,
                'message' => 'プレイリストを更新しました',
                'data' => $this->toResponseRow($playlist->fresh()),
            ]);
        } catch (\Exception $e) {
            Log::error('MusicPlaylistsController@update failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'payload' => $input,
            ]);
            return $this->serverErrorResponse('プレイリストの更新に失敗しました');
        }
    }

    /**
     * プレイリスト削除API（物理削除。一時的に隠す場合は表示フラグを使用）
     * DELETE /admin/music-playlists/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $playlist = MstYoutubePlaylist::query()->find($id);
        if (!$playlist) {
            return $this->notFoundResponse();
        }

        DB::beginTransaction();
        try {
            // 紐付け楽曲・楽曲ごとのタレント紐付けも合わせて削除
            RelYoutubePlaylistMusicVideoTalent::query()
                ->where('youtube_playlist_id', $playlist->id)
                ->delete();
            RelYoutubePlaylistMusicVideo::query()
                ->where('youtube_playlist_id', $playlist->id)
                ->delete();
            $playlist->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'プレイリストを削除しました',
                'data' => ['id' => (int) $id],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MusicPlaylistsController@destroy failed', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('プレイリストの削除に失敗しました');
        }
    }

    /**
     * リクエストを正規化（プレイリストURLが入力された場合は list パラメータを抽出）
     */
    private function normalizeInput(Request $request): array
    {
        return [
            'youtubePlaylistCode' => $this->extractPlaylistCode($request->input('youtubePlaylistCode')),
            'name' => is_string($request->input('name')) ? trim($request->input('name')) : $request->input('name'),
            'isVisible' => $request->input('isVisible'),
        ];
    }

    private function extractPlaylistCode($raw): ?string
    {
        if (!is_string($raw)) {
            return null;
        }
        $value = trim($raw);
        if ($value === '') {
            return null;
        }
        if (preg_match(self::PLAYLIST_CODE_PATTERN, $value)) {
            return $value;
        }
        $query = parse_url($value, PHP_URL_QUERY);
        if (is_string($query)) {
            parse_str($query, $params);
            if (isset($params['list']) && is_string($params['list'])) {
                return trim($params['list']);
            }
        }
        return $value;
    }

    private function rules(?int $ignoreId): array
    {
        $unique = Rule::unique('mst_youtube_playlist', 'youtube_playlist_code');
        if ($ignoreId !== null) {
            $unique = $unique->ignore($ignoreId, 'id');
        }

        return [
            'youtubePlaylistCode' => ['required', 'string', 'regex:' . self::PLAYLIST_CODE_PATTERN, $unique],
            'name' => ['required', 'string', 'max:255'],
            'isVisible' => ['required', 'boolean'],
        ];
    }

    private function messages(): array
    {
        return [
            'youtubePlaylistCode.required' => 'YouTubeプレイリストIDまたはURLを入力してください',
            'youtubePlaylistCode.regex' => 'YouTubeプレイリストIDの形式が正しくありません',
            'youtubePlaylistCode.unique' => 'このプレイリストは既に登録されています',
            'name.required' => 'プレイリスト名を入力してください',
            'name.max' => 'プレイリスト名は255文字以内で入力してください',
            'isVisible.required' => '表示設定を指定してください',
            'isVisible.boolean' => '表示設定の値が正しくありません',
        ];
    }

    private function fill(MstYoutubePlaylist $playlist, array $input): void
    {
        $playlist->youtube_playlist_code = $input['youtubePlaylistCode'];
        $playlist->playlist_name = $input['name'];
        $playlist->view_flag = filter_var($input['isVisible'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
    }

    /**
     * プレイリストIDごとの紐付け楽曲数（削除済みの楽曲は除く）
     * @return array<int, int>
     */
    private function countMusicByPlaylistIds(array $playlistIds): array
    {
        if (count($playlistIds) === 0) {
            return [];
        }
        return RelYoutubePlaylistMusicVideo::query()
            ->join('mst_youtube_music_video', 'mst_youtube_music_video.id', '=', 'rel_youtube_playlist_music_video.youtube_music_video_id')
            ->whereIn('rel_youtube_playlist_music_video.youtube_playlist_id', $playlistIds)
            ->where('mst_youtube_music_video.view_flag', 1)
            ->groupBy('rel_youtube_playlist_music_video.youtube_playlist_id')
            ->selectRaw('rel_youtube_playlist_music_video.youtube_playlist_id as playlist_id, COUNT(*) as music_count')
            ->get()
            ->mapWithKeys(fn ($row) => [(int) $row->playlist_id => (int) $row->music_count])
            ->all();
    }

    private function toResponseRow(MstYoutubePlaylist $playlist, ?int $musicCount = null): array
    {
        $code = (string) ($playlist->youtube_playlist_code ?? '');
        return [
            'id' => (int) $playlist->id,
            'youtubePlaylistCode' => $code,
            'name' => (string) ($playlist->playlist_name ?? ''),
            'url' => self::YOUTUBE_PLAYLIST_URL . rawurlencode($code),
            'isVisible' => (string) $playlist->view_flag === '1',
            'musicCount' => $musicCount,
            'createdAt' => $playlist->created_datetime ?? null,
            'updatedAt' => $playlist->updated_datetime ?? null,
        ];
    }

    private function notFoundResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'プレイリストが見つかりませんでした',
        ], 404);
    }

    private function validationErrorResponse($errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'バリデーションエラーが発生しました',
            'errors' => $errors,
        ], 422);
    }

    private function serverErrorResponse(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }
}
