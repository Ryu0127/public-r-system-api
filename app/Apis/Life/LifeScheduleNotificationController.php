<?php

namespace App\Apis\Life;

use App\Http\Controllers\Controller;
use App\Repositories\TblLifeScheduleNotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LifeScheduleNotificationController extends Controller
{
    private $tblLifeScheduleNotificationRepository;

    public function __construct() {
        $this->tblLifeScheduleNotificationRepository = new TblLifeScheduleNotificationRepository();
    }

    /**
     * スケジュール通知登録API（外部サーバー連携用）
     * POST /sync/life/schedule/notification
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // リクエストパラメータの取得
        $lifeScheduleId = $request->input('life_schedule_id');
        $notificationDateTime = $request->input('notification_date_time');

        // バリデーション：必須パラメータチェック
        if (!$lifeScheduleId) {
            return response()->json([
                'status' => false,
                'message' => 'life_schedule_id parameter is required.'
            ], 400);
        }

        if (!$notificationDateTime) {
            return response()->json([
                'status' => false,
                'message' => 'notification_date_time parameter is required.'
            ], 400);
        }

        // バリデーション：日時フォーマットチェック
        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $notificationDateTime)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid notification_date_time format. Expected YYYY-MM-DD HH:MM:SS format.'
            ], 400);
        }

        // 日時の妥当性チェック
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $notificationDateTime);
        if (!$dateTime || $dateTime->format('Y-m-d H:i:s') !== $notificationDateTime) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid notification_date_time value. Please provide a valid date and time.'
            ], 400);
        }

        // 通知設定の登録
        $entity = [
            'life_schedule_id' => $lifeScheduleId,
            'notification_date_time' => $notificationDateTime,
            'notification_comp_flag' => 0,
            'created_program_name' => 'sync-api',
            'updated_program_name' => 'sync-api',
        ];

        try {
            $notification = $this->tblLifeScheduleNotificationRepository->insert($entity);

            $responseData = [
                'status' => true,
                'message' => 'Notification registered successfully.',
                'data' => [
                    'notificationId' => $notification->id,
                    'lifeScheduleId' => $notification->life_schedule_id,
                    'notificationDateTime' => $notification->notification_date_time,
                    'notificationCompFlag' => $notification->notification_comp_flag,
                ]
            ];

            return response()->json($responseData, 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to register notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * スケジュール通知削除API（外部サーバー連携用）
     * DELETE /sync/life/schedule/notification/{id}
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        // バリデーション：IDチェック
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid notification ID.'
            ], 400);
        }

        try {
            // 通知設定の存在確認
            $notification = $this->tblLifeScheduleNotificationRepository->findPk($id);

            if (!$notification) {
                return response()->json([
                    'status' => false,
                    'message' => 'Notification not found.'
                ], 404);
            }

            // 通知設定の削除
            $this->tblLifeScheduleNotificationRepository->deleteByPk($id);

            $responseData = [
                'status' => true,
                'message' => 'Notification deleted successfully.'
            ];

            return response()->json($responseData);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete notification: ' . $e->getMessage()
            ], 500);
        }
    }
}
