<?php

namespace App\Apis\Life;

use App\Http\Controllers\Controller;
use App\Repositories\TblLifeScheduleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LifeScheduleSyncController extends Controller
{
    private $tblLifeScheduleRepository;

    public function __construct() {
        $this->tblLifeScheduleRepository = new TblLifeScheduleRepository();
    }

    /**
     * スケジュール同期API（外部サーバー連携用）
     * GET /life/schedule-sync
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // リクエストパラメータの取得
        $date = $request->query('date');
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 50);

        // 日付のバリデーション
        if (!$date) {
            return response()->json([
                'status' => false,
                'message' => 'Date parameter is required. Please provide date in YYYY-MM-DD format.'
            ], 400);
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid date format. Expected YYYY-MM-DD format.'
            ], 400);
        }

        // 日付の妥当性チェック
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateTime || $dateTime->format('Y-m-d') !== $date) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid date value. Please provide a valid date.'
            ], 400);
        }

        // perPageのバリデーション（最大200件まで）
        $perPage = min(max((int)$perPage, 1), 200);

        // 指定日以降のスケジュール取得（ページネーション付き）
        $tblLifeSchedules = $this->tblLifeScheduleRepository->paginateAfterDate($date, $perPage);

        $responseData = [
            'status' => true,
            'data' => [
                // ページング情報
                'pagination' => [
                    'total' => $tblLifeSchedules->total(),
                    'perPage' => $tblLifeSchedules->perPage(),
                    'currentPage' => $tblLifeSchedules->currentPage(),
                    'lastPage' => $tblLifeSchedules->lastPage(),
                    'from' => $tblLifeSchedules->firstItem(),
                    'to' => $tblLifeSchedules->lastItem(),
                ],
                // タスク
                'tasks' => $tblLifeSchedules->map(function ($tblLifeSchedule) {
                    return [
                        'taskId' => $tblLifeSchedule->id,
                        'taskName' => $tblLifeSchedule->schedule_contents,
                        'startDateTime' => $tblLifeSchedule->start_date_time,
                        'endDateTime' => $tblLifeSchedule->end_date_time,
                        'scheduleDate' => $tblLifeSchedule->schedule_date,
                        'scheduleType' => $tblLifeSchedule->schedule_type,
                        'remarks' => $tblLifeSchedule->remarks,
                        'notificationRequestFlag' => $tblLifeSchedule->notification_request_flag,
                        'notificationCompFlag' => $tblLifeSchedule->notification_comp_flag,
                    ];
                })->values(),
            ]
        ];

        return response()->json($responseData);
    }
}
