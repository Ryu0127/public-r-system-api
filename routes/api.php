<?php

use App\Apis\Auth\LoginController;
use App\Apis\Auth\AutoLoginController;
use App\Apis\Auth\LogoutController;
use App\Apis\Events\EventsController;
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
 * 認証システム
 * --------------------------------------------------------------------------
 */
// ドメイン制限ルート
Route::middleware(['check.origin'])->group(function () {
    // POST:認証-ログイン（メールアドレス+パスワード認証）
    Route::post('/auth/login', [LoginController::class, 'doLogin']);
    // POST:認証-自動ログイン（自動ログイントークン認証）
    Route::post('/auth/auto-login', [AutoLoginController::class, 'doAutoLogin']);
    // POST:認証-ログアウト（自動ログイントークン無効化）
    Route::post('/auth/logout', [LogoutController::class, 'doLogout']);
});

/*
 * --------------------------------------------------------------------------
 * 生活管理システム
 * --------------------------------------------------------------------------
 */
// トークン認証 + ドメイン制限ルート
Route::middleware(['auth.token', 'check.origin'])->group(function () {
    // GET:LifeSystem-日次スケジュールタスク（データ取得API）
    Route::get('/life/schedule-day/tasks/{date}', [LifeScheduleDayTaskController::class, 'index']);
    // POST:LifeSystem-日次スケジュールタスク（データ更新API）
    Route::post('/life/schedule-day/tasks', [LifeScheduleDayTaskController::class, 'doUpdate']);
    // GET:LifeSystem-月次スケジュールタスク（データ取得API）
    Route::get('/life/schedule-month/tasks/{yearMonth}', [LifeScheduleMonthTaskController::class, 'index']);
});

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
Route::get('/oshi-katsu-saport/talents/{id}/hashtags', [OshiKatsuSaportController::class, 'talentHashtags']);
// GET:推し活サポート-エゴサーチ用タレント一覧取得API
Route::get('/oshi-katsu-saport/ego-search/talents', [OshiKatsuSaportController::class, 'egoSearchTalents']);

/*
 * --------------------------------------------------------------------------
 * イベント管理
 * --------------------------------------------------------------------------
 */
// トークン認証 + ドメイン制限ルート
// Route::middleware(['auth.token', 'check.origin'])->group(function () {
    // GET:イベント-イベント一覧取得API
    Route::get('/admin/events', [EventsController::class, 'index']);
    // GET:イベント-イベント詳細取得API
    Route::get('/admin/events/{id}', [EventsController::class, 'show']);
    // POST:イベント-イベント登録API（複数件対応）
    Route::post('/admin/events', [EventsController::class, 'store']);
// });
