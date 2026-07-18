<?php

namespace App\Apis\Showtimes;

use App\Apis\Showtimes\Mock\TdlShowtimesMock;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowtimesController extends Controller
{
    /**
     * TDL ショー&パレード / 混雑データ取得API
     * GET /showtimes/tdl?date=2026-07-21
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function tdl(Request $request): JsonResponse
    {
        $date = $request->query('date', '2026-07-21');

        if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json([
                'status' => false,
                'message' => 'date は YYYY-MM-DD 形式で指定してください',
            ], 400);
        }

        return response()->json([
            'status' => true,
            'data' => TdlShowtimesMock::forDate($date),
        ]);
    }
}
