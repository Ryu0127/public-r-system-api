<?php

namespace App\Apis\Life;

use App\Http\Controllers\Controller;
use App\Repositories\TblLifeScheduleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LifeScheduleMonthTaskController extends Controller
{
    private $tblLifeScheduleRepository;

    public function __construct() {
        $this->tblLifeScheduleRepository = new TblLifeScheduleRepository();
    }

    /**
     * 月次スケジュールタスク取得API
     * GET /life/schedule-month/tasks/{yearMonth}
     *
     * @param Request $request
     * @param string $yearMonth 年月 (YYYY-MM形式)
     * @return JsonResponse
     */
    public function index(Request $request, string $yearMonth): JsonResponse
    {
        // 年月のバリデーション (YYYY-MM形式)
        if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid year-month format. Expected YYYY-MM format.'
            ], 400);
        }
        
        // 年月から開始日と終了日を生成
        $startDate = $yearMonth . '-01';
        $endDate = date('Y-m-t', strtotime($startDate)); // 月の最終日
        // 指定年月のスケジュール取得
        $tblLifeSchedules = $this->tblLifeScheduleRepository->selectByYearMonth($startDate, $endDate);

        $responseData = [
            'status' => true,
            'data' => [
                // タスク
                'tasks' => $tblLifeSchedules->map(function ($tblLifeSchedule) {
                    return [
                        'taskId' => $tblLifeSchedule->id,
                        'taskName' => $tblLifeSchedule->schedule_contents,
                        'startDateTime' => $tblLifeSchedule->start_date_time,
                        'endDateTime' => $tblLifeSchedule->end_date_time,
                        'scheduleDate' => $tblLifeSchedule->schedule_date,
                        'projectId' => $tblLifeSchedule->id,
                        'remarks' => $tblLifeSchedule->remarks,
                    ];
                })->values(),
            ]
        ];
        return response()->json($responseData);
    }
}
