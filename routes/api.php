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

// 회원가입, 로그인
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 약 정보 조회
Route::apiResource('drugs', DrugController::class);

// 리뷰 조회
Route::get('/reviews/{item_seq}', [ReviewController::class, 'show']);

// 로드밸런서 헬스체크
Route::get('/health', function () {
    return response()->json(['status' => 'OK']);
});


// 모든 API 경로에 인증을 요구하는 그룹
Route::middleware('auth:api')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('reviews', ReviewController::class);

    // 공지사항
    Route::apiResource('notices', NoticeController::class);

    // 최근 한건의 공지 확인
    Route::get('/notices/recent', [NoticeController::class, 'recent']);

    // 약 복용 로그
    Route::apiResource('medication-logs', MedicationLogController::class);
    // 예전 복용 로그
    Route::get('/old-medication-logs', [MedicationLogController::class, 'oldLogs']);
    // 재복용 처리
    Route::patch('medication-logs/{id}/reuse', [MedicationLogController::class, 'reuse']);

    // 개인 정보 수정
    Route::patch('/users', [UserController::class, 'update']);

    // 탈퇴
    Route::delete('/users', [UserController::class, 'update']);

    // 공공 데이터 API
    Route::get('/fetch-store-drugs', [OpenApiController::class, 'fetchDrugs']);
    Route::get('/fetch-store-drugs/progress', [OpenApiController::class, 'getProgress']);

    // 로그아웃
    Route::post('/logout', [AuthController::class, 'logout']);

    // 이미지 업로드
    Route::post('/images/upload', [ImageController::class, 'upload']);
    Route::delete('/images/delete', [ImageController::class, 'delete']);
});
