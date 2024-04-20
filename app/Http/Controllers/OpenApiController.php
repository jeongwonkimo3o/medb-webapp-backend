<?php

namespace App\Http\Controllers;

use App\Jobs\DispatchDrugFetchingJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenApiController extends Controller
{
    public function __construct()
    {
        // 모든 요청에 대해 API 인증 및 관리자 권한 확인
        $this->middleware('auth:api');
        $this->middleware('is_admin');

        date_default_timezone_set('Asia/Seoul');
    }

    /**
     * API로부터 전체 페이지 수를 동적으로 조회한 후, 해당 페이지 수에 따라 작업을 디스패치
     */
    public function fetchDrugs()
    {
        $url = config('api.url');
        $serviceKey = config('api.service_key');

        // API에서 전체 데이터 수를 가져와서 페이지 수 계산
        try {
            $response = Http::get($url, [
                'serviceKey' => $serviceKey,
                'pageNo' => 1,
                'numOfRows' => 1,
                'type' => 'json'
            ]);



            if ($response->successful()) {
                $data = $response->json();
                $totalCount = $data['body']['totalCount'];
                $totalPages = ceil($totalCount / 100);  // 100은 한 페이지당 항목 수

                Log::info("API 응답 성공: 전체 데이터 수 {$totalCount}, 계산된 페이지 수 {$totalPages}");

                // 페이지 수에 따라 작업 디스패치
                DispatchDrugFetchingJobs::dispatch($totalPages);

                return response()->json(['message' => 'OPEN API 데이터 가져오기 및 저장이 시작되었습니다.'], 200);
            } else {
                Log::error('API 통신에 실패하였습니다.', ['response' => $response->body()]);
                return response()->json(['message' => 'API 호출에 실패하였습니다.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('API 호출 중 예외가 발생하였습니다.', ['message' => $e->getMessage()]);
            return response()->json(['message' => '처리 중 오류가 발생했습니다.'], 500);
        }
    }

    // 최종 업데이트 일자 조회하는 API
    public function getProgress()
    {
        // 캐시에서 최종 업데이트 일자를 조회
        $lastUpdated = Cache::get('last_updated');

        // 만약 모든 페이지가 처리되었다면, 최종 업데이트 일자를 반환
        if ($lastUpdated) {
            return response()->json(['lastUpdated' => $lastUpdated]);
        } else {
            // 만약 아직 작업이 완료되지 않았다면, 아직 업데이트 되지 않았음을 알리는 메시지 반환
            return response()->json(['message' => '진행 중인 작업이 아직 완료되지 않았습니다.']);
        }
    }
}
