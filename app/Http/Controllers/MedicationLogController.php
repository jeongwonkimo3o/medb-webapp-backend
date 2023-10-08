<?php

namespace App\Http\Controllers;

use App\Models\MedicationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MedicationLogController extends Controller
{

    private $medi_logs;

    public function __construct(MedicationLog $medi_log)
    {
        $this->middleware('auth:api');
        $this->medi_logs = $medi_log;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medi_logs = $this->medi_logs->all();
        return response()->json($medi_logs, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'drug_name' => 'required',
            'drug_information' => 'required',
            'start_date' => 'required',
        ]);

        // 유효성 검증 실패 시 에러 메시지
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 현재 로그인한 사용자 가져오기
        $user = Auth::user();

        // MedicationLog 인스턴스 생성 및 데이터 저장
        $medi_log = MedicationLog::create([
            'user_id' => $user->user_id,
            'drug_name' => $request->drug_name,
            'drug_information' => $request->drug_information,
            'start_date' => $request->start_date
        ]);

        $medi_log->save();

        return response()->json(['message' => 'medication log created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
