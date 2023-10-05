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
        $this->middleware('auth:api')->except(['index', 'show']);
        $this->memos = $memo;
    }

    // 전체 메모 보기
    public function index()
    {
        $memos = $this->memos->all();
        return response()->json($memos, 200);
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

        // 만약 로그인한 사용자가 없다면 오류 메시지 반환
        if (!$user) {
            return response()->json(['message' => 'You must be logged in to create a memo'], 401);
        }

        // 객체 생성
        $memos = new Memo;

        // 사용자 ID 설정
        $memos->user_id = $user->user_id;

        // 요청으로부터 내용 설정
        $memos->content = $request->content;

        // DB에 저장
        $memos->save();

        return response()->json(['message' => 'Memo created successfully'], 201);
    }


    // 특정 id 메모 보기
    public function show(string $id)
    {
        $memos = $this->memos->find($id);

        if (!$memos) {
            return response()->json(['message' => 'Cannot find the memo'], 404);
        }

        return response()->json(['memo' => $memos, 'message' => 'The memo has been successfully retrieved'], 200);
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
