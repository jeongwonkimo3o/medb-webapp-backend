<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\MedicationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MedicationLogController extends Controller
{

    private $logModel;

    public function __construct(MedicationLog $medi_log)
    {
        $this->middleware('auth:api');
        $this->logModel = $medi_log;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medi_logs = $this->logModel
            ->where('user_id', Auth::id())
            ->whereNull('end_date')
            ->get();
        $logs_with_details = [];
        $total_drugs = 0; // 복용 중인 약의 총 개수를 저장할 변수 초기화

        foreach ($medi_logs as $log) {
            $drug = $log->drug()->first();
            $logs_with_details[] = [
                'log_id' => $log->id,
                'created_at' => $log->created_at,
                'drug_details' => $drug,
            ];
            $total_drugs++; // 약 개수 증가
        }

        return response()->json([
            'total_drugs' => $total_drugs, // 약 개수 반환
            'logs_with_details' => $logs_with_details,
            'message' => '약 정보가 성공적으로 조회되었습니다.'
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'item_seq' => 'required', // start_date 필드 제거
        ]);

        // 유효성 검증 실패 시 에러 메시지
        if ($validator->fails()) {
            return response()->json([
                'message' => '유효성 검사 실패',
                'errors' => $validator->errors()
            ], 422);
        }

        // item_seq 값을 받아서 해당하는 drug를 찾음
        $itemSeq = $request->input('item_seq'); // 요청 본문(body)에서 item_seq 값을 가져옴
        $drug = Drug::where('item_seq', $itemSeq)->first();

        if (!$drug) {
            return response()->json(['message' => '약 정보를 찾을 수 없습니다.'], 404);
        }

        // 이미 해당 drug_id로 등록된 복용 정보가 있는지 확인
        $existingMediLog = MedicationLog::where('user_id', Auth::id())
            ->where('drug_id', $drug->id)
            ->exists();

        if ($existingMediLog) {
            return response()->json(['message' => '이미 복용 정보가 등록되어 있습니다.'], 400);
        }

        // MedicationLog 모델 인스턴스 생성
        $medi_log = new MedicationLog();
        $medi_log->user_id = Auth::id();
        $medi_log->drug_id = $drug->id;
        $medi_log->save();

        return response()->json(['message' => '복용 정보가 정상적으로 등록되었습니다.'], 201);
    }

    public function reuse(Request $request, string $id)
    {
        // 로그의 end_date를 null로 업데이트하여 재사용 처리
        $medi_log = $this->logModel->where('id', $id)->where('user_id', Auth::id())->first();

        // 로그가 없거나 현재 사용자가 소유한 로그가 아닌 경우
        if (!$medi_log) {
            return response()->json([
                'message' => 'log not found or permission denied',
                'errors' => null
            ], 403);
        }

        // 해당 id에 대한 로그의 end_date를 null로 업데이트
        $medi_log->update(['end_date' => null]);

        return response()->json(['message' => '로그가 재사용되었습니다.'], 200);
    }


    public function oldLogs(Request $request)
    {
        $medi_logs = $this->logModel
            ->where('user_id', Auth::id())
            ->whereNotNull('end_date')
            ->get();
        $logs_with_details = [];
        $total_drugs = 0; // 복용 중인 약의 총 개수를 저장할 변수 초기화

        foreach ($medi_logs as $log) {
            $drug = $log->drug()->first();
            $logs_with_details[] = [
                'log_id' => $log->id,
                'created_at' => $log->created_at,
                'end_date' => $log->end_date,
                'drug_details' => $drug,
            ];
            $total_drugs++; // 약 개수 증가
        }

        return response()->json([
            'total_drugs' => $total_drugs, // 약 개수 반환
            'logs_with_details' => $logs_with_details,
            'message' => '약 정보가 성공적으로 조회되었습니다.'
        ], 200);
    }


    public function update(Request $request, string $id)
    {
        // 로그의 end_date만 업데이트
        $medi_log = $this->logModel->where('id', $id)->where('user_id', Auth::id())->first();

        // 로그가 없거나 현재 사용자가 소유한 로그가 아닌 경우
        if (!$medi_log) {
            return response()->json([
                'message' => 'log not found or permission denied',
                'errors' => null
            ], 403);
        }

        // 해당 id에 대한 로그의 end_date를 현재 시간으로 업데이트
        $medi_log->update(['end_date' => now()]);

        return response()->json(['message' => '로그가 정상적으로 업데이트 되었습니다.'], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $medi_log = $this->logModel->where('log_id', $id)->where('user_id', Auth::id())->first();

        // 로그가 없거나 현재 사용자가 소유한 로그가 아닌 경우
        if (!$medi_log) {
            return response()->json([
                'message' => 'log not found or permission denied',
                'errors' => null
            ], 403);
        }

        $medi_log->delete();

        return response()->json(['message' => 'log deleted successfully'], 200);
    }
}
