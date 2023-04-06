<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/dbIO', [ApiController::class, 'DatabaseIOPerformanceTest']);
Route::get('/discIO', [ApiController::class, 'DiscIOPerformanceTest']);
Route::get('/gc', [ApiController::class, 'GarbageCollectionPerformanceTest']);
Route::get('/tp', [ApiController::class, 'ThreadPerformanceTest']);

