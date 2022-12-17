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
    ->prefix('order')
    ->group(function(){
            Route::get("all-orders", [WorkerShiftController::class, 'getAllOrders'])->name('all-orders');
            Route::get('top-distributors', [WorkerShiftController::class, 'topDistributors'])->name('top-distributors');
            Route::post('search', [WorkerShiftController::class, 'search'])->name('search');

        }
    );
