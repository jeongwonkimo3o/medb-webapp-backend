<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MemoController extends Controller
{
    private $memos;

    public function __construct(Memo $memo)
    {
        $this->middleware('auth:api');
        $this->memos = $memo;
    }

    // 전체 메모 보기
    public function index()
    {
        // 자신의 메모만 가져와야 함
        $user = Auth::user();

        // 10개씩 페이지네이션
        $memos = $this->memos->where('user_id', $user->user_id)->paginate(10);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 현재 로그인한 사용자 가져오기
        $user = Auth::user();

        // 객체 생성
        $memos = $this->memos;

        // 사용자 ID 설정
        $memos->user_id = $user->user_id;

        // 요청으로부터 내용 설정
        $memos->content = $request->content;

        // DB에 저장
        $memos->save();

        return response()->json(['message' => 'Memo created successfully'], 201);
    }

    // 메모 수정
    public function update(Request $request, string $id)
    {
        // put으로 요청 보낼 경우 거부
        if ($request->isMethod('put')) {
            return response()->json(['message' => 'PUT method is not allowed'], 405);
        }

        $user = Auth::user();

        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ]);

        // 유효성 검증 실패 시 에러 메시지
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $memo = Memo::where('memo_id', $id)->where('user_id', $user->user_id)->first();

        // 메모가 없거나 현재 사용자가 소유한 메모가 아닌 경우
        if (!$memo) {
            return response()->json(['message' => 'Memo not found or you do not have permission to update this memo'], 403);
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
        $user = Auth::user();

        $memo = Memo::where('memo_id', $id)->where('user_id', $user->user_id)->first();

        // 메모가 없거나 현재 사용자가 소유한 메모가 아닌 경우
        if (!$memo) {
            return response()->json(['message' => 'Memo not found or you do not have permission to update this memo'], 403);
        }

        $memo->delete();

        return response()->json(['message' => 'Memo deleted successfully'], 200);
    }
}
