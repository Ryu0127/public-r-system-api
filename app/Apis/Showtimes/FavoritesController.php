<?php

namespace App\Apis\Showtimes;

use App\Contexts\Application\Services\Showtimes\DisneyParkFavoriteApplicationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class FavoritesController extends Controller
{
    private DisneyParkFavoriteApplicationService $disneyParkFavoriteApplicationService;

    public function __construct(
        DisneyParkFavoriteApplicationService $disneyParkFavoriteApplicationService
    ) {
        $this->disneyParkFavoriteApplicationService = $disneyParkFavoriteApplicationService;
    }

    /**
     * お気に入り一覧取得API
     * GET /showtimes/favorites
     */
    public function index(Request $request): JsonResponse
    {
        $userId = (int) $request->input('authenticated_user_id');
        $data = $this->disneyParkFavoriteApplicationService->listByUserId($userId);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * お気に入り登録API
     * POST /showtimes/favorites
     */
    public function store(Request $request): JsonResponse
    {
        $userId = (int) $request->input('authenticated_user_id');
        $favoriteType = (int) $request->input('favoriteType');
        $targetId = (int) $request->input('targetId');

        if ($favoriteType <= 0 || $targetId <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'favoriteType と targetId は必須です',
            ], 400);
        }

        try {
            $this->disneyParkFavoriteApplicationService->add($userId, $favoriteType, $targetId);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * お気に入り解除API
     * DELETE /showtimes/favorites/{favoriteType}/{targetId}
     */
    public function destroy(Request $request, int $favoriteType, int $targetId): JsonResponse
    {
        $userId = (int) $request->input('authenticated_user_id');

        if ($favoriteType <= 0 || $targetId <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'favoriteType と targetId は必須です',
            ], 400);
        }

        try {
            $this->disneyParkFavoriteApplicationService->remove($userId, $favoriteType, $targetId);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'status' => true,
        ]);
    }
}
