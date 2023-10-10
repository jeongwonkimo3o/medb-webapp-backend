<?php

namespace App\Http\Controllers;

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
        // 10개씩 페이지네이션
        $medi_logs = $this->logModel->where('user_id', Auth::id())->paginate(10);
        return response()->json(['log' => $medi_logs, 'message' => 'The logs has been successfully retrieved'], 200);
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
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $medi_log = $this->logModel;
        $medi_log->fill($request->all()); // model fillable 속성 참조
        $medi_log->user_id = Auth::id();
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
        // put으로 요청 보낼 경우 거부
        if ($request->isMethod('put')) {
            return response()->json([
                'message' => 'PUT method is not allowed',
                'errors' => null
            ], 405);
        }

        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'drug_name' => 'sometimes|required',
            'drug_information' => 'sometimes|required',
            'start_date' => 'sometimes|required',
        ]);

       // 유효성 검증 실패 시 에러 메시지
         if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $medi_log = $this->logModel->where('log_id', $id)->where('user_id', Auth::id())->first();

        // 로그가 없거나 현재 사용자가 소유한 로그가 아닌 경우
        if (!$medi_log) {
            return response()->json([
                'message' => 'log not found or permission denied',
                'errors' => null
            ], 403);
        }

        $medi_log->update($request->all());

        return response()->json(['message' => 'log content updated successfully'], 200);
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
