<?php

use App\Apis\Auth\LoginController;
use App\Apis\Auth\AutoLoginController;
use App\Apis\Auth\LogoutController;
use App\Apis\Life\LifeScheduleDayTaskController;
use App\Apis\Life\LifeScheduleMonthTaskController;
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
 * 認証システム
 * --------------------------------------------------------------------------
 */
// POST:認証-ログイン（メールアドレス+パスワード認証）
Route::post('/auth/login', LoginController::class);
// POST:認証-自動ログイン（自動ログイントークン認証）
Route::post('/auth/auto-login', AutoLoginController::class);
// POST:認証-ログアウト（自動ログイントークン無効化）
Route::post('/auth/logout', LogoutController::class);

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