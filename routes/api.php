<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WorkerShiftController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::middleware([])->name('api')
    ->prefix('worker/shift')
    ->group(function(){
            Route::post("clock-in", [WorkerShiftController::class, 'workerClockIn'])->name('.clock-in');
            Route::post('clock-out', [WorkerShiftController::class, 'workerClockOut'])->name('.clock-out');
            Route::post('work-days', [WorkerShiftController::class, 'listOfAllShiftForAWorker'])->name('.work-days');
        }
    );
