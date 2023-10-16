<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    protected $withdrawalModel;

    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawalModel = $withdrawal;
        $this->middleware('auth:api')->only(['store']);
        $this->middleware('is_admin')->only(['index', 'show']);
    }

    public function index()
    {
        $withdrawals = $this->withdrawalModel->all();
        return response()->json(['withdrawal' => $withdrawals, 'message' => 'The withdrawals has been successfully retrieved'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required',
            'improvement' => 'required',
        ]);

        // 유효성 검증 실패 시 에러 메시지
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // 객체 생성
        $withdrawal = $this->withdrawalModel;

        // 사용자 ID 설정
        $withdrawal->user_id = Auth::id();

        // 요청으로부터 내용 설정
        $withdrawal->reason = $request->reason;
        $withdrawal->improvement = $request->improvement;

        // DB에 저장
        $withdrawal->save();

        return response()->json(['message' => 'withdrawal created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $withdrawal = $this->withdrawalModel->find($id);
        if (!$withdrawal) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        return response()->json($withdrawal);
    }

}
