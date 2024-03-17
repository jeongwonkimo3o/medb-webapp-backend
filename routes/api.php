<?php

// use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MedicationLogController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\OpenApiController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// 인증이 필요 없는 경로
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('drugs', DrugController::class);

// 모든 API 경로에 인증을 요구하는 그룹
Route::middleware('auth:api')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('memos', MemoController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('notices', NoticeController::class);
    Route::apiResource('medication-logs', MedicationLogController::class);

    Route::patch('/users', [UserController::class, 'update']);
    Route::delete('/users', [UserController::class, 'update']);


    // Route::get('/fetch-store-drugs', [OpenApiController::class, 'fetchDrugs']);
    Route::get('/old-medication-logs', [MedicationLogController::class, 'oldLogs']);
    Route::patch('medication-logs/{id}/reuse', [MedicationLogController::class, 'reuse']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/images/upload', [ImageController::class, 'upload']);
    Route::delete('/images/delete', [ImageController::class, 'delete']);
});
