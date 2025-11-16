<?php

use App\Apis\Home\HomeController;
use App\Apis\Life\LifeScheduleDayTaskController;
use App\Apis\Life\LifeScheduleMonthTaskController;
use App\Apis\OshiKatsuSaport\OshiKatsuSaportController;
use App\Apis\Sync\Life\LifeScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
 * --------------------------------------------------------------------------
 * 生活管理システム
 * --------------------------------------------------------------------------
 */
// GET:LifeSystem-日次スケジュールタスク（データ取得API）
Route::get('/life/schedule-day/tasks/{date}', [LifeScheduleDayTaskController::class, 'index']);
// POST:LifeSystem-日次スケジュールタスク（データ更新API）
Route::post('/life/schedule-day/tasks', [LifeScheduleDayTaskController::class, 'doUpdate']);
// GET:LifeSystem-月次スケジュールタスク（データ取得API）
Route::get('/life/schedule-month/tasks/{yearMonth}', [LifeScheduleMonthTaskController::class, 'index']);

/*
 * --------------------------------------------------------------------------
 * 外部連携システム
 * --------------------------------------------------------------------------
 */
// GET:Sync-生活スケジュール同期API（外部サーバー連携用・ページング対応）
Route::get('/sync/life/schedule', [LifeScheduleController::class, 'index']);

/*
 * --------------------------------------------------------------------------
 * ホーム画面
 * --------------------------------------------------------------------------
 */
// GET:ホーム-機能一覧取得API
Route::get('/home/features', [HomeController::class, 'features']);
// GET:ホーム-更新履歴取得API
Route::get('/home/change-logs', [HomeController::class, 'changeLogs']);
// GET:ホーム-期間限定トピック取得API
Route::get('/home/limited-time-topic', [HomeController::class, 'limitedTimeTopic']);

/*
 * --------------------------------------------------------------------------
 * 推し活サポート
 * --------------------------------------------------------------------------
 */
// GET:推し活サポート-タレント一覧取得API
Route::get('/oshi-katsu-saport/talents', [OshiKatsuSaportController::class, 'talents']);
// GET:推し活サポート-タレント別ハッシュタグ取得API
Route::get('/oshi-katsu-saport/talents/{talentKey}/hashtags', [OshiKatsuSaportController::class, 'talentHashtags']);