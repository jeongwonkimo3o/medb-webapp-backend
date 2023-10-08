<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            return response()->json(['errors' => $validator->errors()], 400);
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
        //
    }

    // 메모 삭제
    public function destroy(string $id)
    {
        //
    }
}
