<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MedicationLogController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\NoticeController;
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

// resource
// ^ api/users
Route::apiResource('users', UserController::class);

// ^ api/memos
Route::apiResource('memos', MemoController::class);

// ^ api/reviews
Route::apiResource('reviews', ReviewController::class);

// ^ api/notices
Route::apiResource('notices', NoticeController::class);

// ^ api/medication-logs
Route::apiResource('medication-logs', MedicationLogController::class);

Route::apiResource('withdrawals', WithdrawalController::class);


Route::get('/feedbacks', [FeedbackController::class, 'store']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/images/upload', [ImageController::class, 'upload']);
Route::delete('/images/delete', [ImageController::class, 'delete']);

