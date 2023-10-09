<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MemoController extends Controller
{
    private $memoModel;

    public function __construct(Memo $memo)
    {
        $this->middleware('auth:api');
        $this->memoModel = $memo;
    }

    // 전체 메모 보기
    public function index()
    {

        // 10개씩 페이지네이션
        $memos = $this->memoModel->where('user_id', Auth::id())->paginate(10);
        return response()->json(['memo' => $memos, 'message' => 'The memo has been successfully retrieved'], 200);
    }

    // 메모 작성
    public function store(Request $request)
    {

        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ]);

        // 유효성 검증 실패 시 에러 메시지
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // 객체 생성
        $memo = $this->memoModel;

        // 사용자 ID 설정
        $memo->user_id = Auth::id();

        // 요청으로부터 내용 설정
        $memo->content = $request->content;

        // DB에 저장
        $memo->save();

        return response()->json(['message' => 'Memo created successfully'], 201);
    }

    // 메모 수정
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
            'content' => 'required',
        ]);

        // 유효성 검증 실패 시 에러 메시지
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $memo = Memo::where('memo_id', $id)->where('user_id', Auth::id())->first();

        // 메모가 없거나 현재 사용자가 소유한 메모가 아닌 경우
        if (!$memo) {
            return response()->json([
                'message' => 'Memo not found or permission denied',
                'errors' => null
            ], 403);
        }

        // 메모의 content 값을 수정하기
        $memo->content = $request->input('content');

        // 수정된 값을 데이터베이스에 저장하기
        $memo->save();

        return response()->json(['message' => 'Memo content updated successfully'], 200);
    }

    // 메모 삭제
    public function destroy(string $id)
    {

        $memo = Memo::where('memo_id', $id)->where('user_id', Auth::id())->first();

        // 메모가 없거나 현재 사용자가 소유한 메모가 아닌 경우
        if (!$memo) {
            return response()->json([
                'message' => 'Memo not found or permission denied',
                'errors' => null
            ], 403);
        }

        $memo->delete();

        return response()->json(['message' => 'Memo deleted successfully'], 200);
    }
}
