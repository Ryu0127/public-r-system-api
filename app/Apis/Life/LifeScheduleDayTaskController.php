<?php

namespace App\Apis\Life;

use App\Http\Controllers\Controller;
use App\Repositories\TblLifeScheduleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LifeScheduleDayTaskController extends Controller
{
    private $tblLifeScheduleRepository;

    public function __construct() {
        $this->tblLifeScheduleRepository = new TblLifeScheduleRepository();
    }

    /**
     * 日次スケジュールタスク取得API
     * GET /life/schedule-day/tasks/{date}
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request, string $date): JsonResponse
    {
        $tblLifeSchedules = $this->tblLifeScheduleRepository->afterScheduleDate($date);
        $responseData = [
            'status' => true,
            'data' => [
                // 勤務種別
                'workTypeOptions' => $this->mapToWorkTypeOptions()->map(function ($workTypeOption) {
                    return [
                        'label' => $workTypeOption['label'],
                        'value' => $workTypeOption['value'],
                    ];
                }),
                // タスク
                'tasks' => $tblLifeSchedules->filter(function ($tblLifeSchedule) use ($date) {
                    // スケジュール開始日がリクエスト日の翌日5時以降の場合は除外
                    return $tblLifeSchedule->schedule_date < date('Y-m-d', strtotime($date . ' +1 day 05:00:00'));
                })->map(function ($tblLifeSchedule) {
                    return [
                        'taskId' => $tblLifeSchedule->id,
                        'taskName' => $tblLifeSchedule->schedule_contents,
                        'startDateTime' => $tblLifeSchedule->start_date_time,
                        'endDateTime' => $tblLifeSchedule->end_date_time,
                        'projectId' => $tblLifeSchedule->id,
                        'remarks' => $tblLifeSchedule->remarks,
                    ];
                })->values(),
            ]
        ];
        return response()->json($responseData);
    }

    /**
     * タスクの更新
     * PUT /life/schedule-day/tasks
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function doUpdate(Request $request): JsonResponse
    {
        // リクエストパラメータの取得
        $tasks = $request['tasks'];
        // タスクの更新
        foreach ($tasks as $task) {
            if (filled($task['taskId']) && blank($task['taskName'])) {
                // スケジュールの削除
                $this->tblLifeScheduleRepository->deleteByPk($task['taskId']);
                continue;
            }
            if (filled($task['taskId']) && filled($task['taskName'])) {
                // スケジュールの更新
                $tblLifeScheduleUpdateEntity = $this->mapToTblLifeScheduleEntityUpdate($task);
                $this->tblLifeScheduleRepository->updateByPk($task['taskId'], $tblLifeScheduleUpdateEntity);
                continue;
            }
            // スケジュールの追加
            $tblLifeScheduleInsertEntity = $this->mapToTblLifeScheduleEntityInsert($task, $request['scheduleDate']);
            $this->tblLifeScheduleRepository->insert($tblLifeScheduleInsertEntity);
        }
        return response()->json([
            'status' => true
        ]);
    }

    private function mapToWorkTypeOptions() {
        return collect([
            $this->mapToOption('', '選択してください'),
            $this->mapToOption('10', '出勤'),
            $this->mapToOption('11', '休日出勤'),
            $this->mapToOption('20', '休日'),
            $this->mapToOption('21', '会社指定休暇'),
            $this->mapToOption('22', '代休消化'),
            $this->mapToOption('30', '有給休暇(全休)'),
            $this->mapToOption('31', '有給休暇(午前休)'),
            $this->mapToOption('32', '有給休暇(午後休)'),
            $this->mapToOption('90', '遅刻'),
            $this->mapToOption('91', '早退'),
            $this->mapToOption('92', '遅刻+早退'),
            $this->mapToOption('93', '欠勤'),
        ]);
    }

    private function mapToOption($value, $label) {
        return [
            'value' => $value,
            'label' => $label,
        ];
    }

    private function mapToTblLifeScheduleEntityInsert($requestTask, $scheduleDate) {
        return [
            'schedule_date' => $scheduleDate,
            'start_date_time' => $requestTask['startDateTime'],
            'end_date_time' => $requestTask['endDateTime'],
            'schedule_contents' => $requestTask['taskName'],
            'notification_request_flag' => 0,
            'notification_comp_flag' => 0,
            'remarks' => $requestTask['remarks'],
        ];
    }

    private function mapToTblLifeScheduleEntityUpdate($requestTask) {
        return [
            'start_date_time' => $requestTask['startDateTime'],
            'end_date_time' => $requestTask['endDateTime'],
            'schedule_contents' => $requestTask['taskName'],
            'remarks' => $requestTask['remarks'],
        ];
    }
}