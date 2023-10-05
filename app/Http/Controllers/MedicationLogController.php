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
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // 현재 로그인한 사용자 가져오기
        $user = Auth::user();

        // 만약 로그인한 사용자가 없다면 오류 메시지 반환
        if (!$user) {
            return response()->json(['message' => 'You must be logged in to create a memo'], 401);
        }

        // 객체 생성
        $medi_logs = new MedicationLog;

        // 사용자 ID 설정
        $medi_logs->user_id = $user->user_id;

        $medi_logs::create([
            'drug_name' => $request->drug_name,
            'drug_information' => $request->drug_information,
            'start_date' => $request->start_date
        ]);

        return response()->json(['message' => 'Memo created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


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
