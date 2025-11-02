<?php

use App\Apis\Life\LifeScheduleDayTaskController;
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