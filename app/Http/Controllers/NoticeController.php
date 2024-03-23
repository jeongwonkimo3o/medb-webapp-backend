<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NoticeController extends Controller
{
    protected $noticeModel;

    public function __construct(Notice $notice)
    {
        $this->noticeModel = $notice;
        $this->middleware('auth:api')->except(['index', 'show']);
        $this->middleware('is_admin')->except(['index', 'show']);
    }

    // 전체 공지 리스트
    public function index()
    {
        $notices = $this->noticeModel->orderBy('created_at', 'desc') // 최신 날짜 순
            ->paginate(10);
        return response()->json(['notices' => $notices, 'message' => 'The notices has been successfully retrieved'], 200);
    }

    // 공지 작성(관리자, is_admin == 1인 경우에만 가능)
    public function store(Request $request)
    {
        // 현재 인증된 사용자가 관리자인지 확인
        $currentUser = Auth::user();
        if ($currentUser->is_admin != 1) {
            return response()->json(['message' => '권한이 없습니다.'], 403);
        }

        // 제목과 내용 검증
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        // 공지사항 생성 및 사용자 ID 할당
        $notice = new Notice;
        $notice->title = $validatedData['title'];
        $notice->content = $validatedData['content'];
        $notice->user_id = $currentUser->id; // 현재 인증된 사용자의 ID를 user_id로 설정
        $notice->save();

        // 응답 반환
        return response()->json(['message' => '공지사항이 성공적으로 작성되었습니다.'], 201);
    }

    // 최근 한건의 공지 확인
    public function recent()
    {
        $notice = $this->noticeModel->orderBy('created_at', 'desc')->first();
        if (!$notice) {
            return response()->json(['message' => '공지를 찾을 수 없습니다.'], 404);
        }
        return response()->json(['notice' => $notice, 'message' => '공지사항 조회가 완료되었습니다.'], 200);
    }

    // 특정 id의 공지 확인
    public function show(string $id)
    {
        $notice = $this->noticeModel->find($id);
        if (!$notice) {
            return response()->json(['message' => 'The memo does not exist'], 404);
        }
        return response()->json(['notice' => $notice, 'message' => 'The notice has been successfully retrieved'], 200);
    }

    // 공지 업데이트(관리자)
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'title' => 'sometimes|required|max:255',
            'content' => 'sometimes|required',
        ]);

        $notice = $this->noticeModel->find($id);
        if (!$notice) {
            return response()->json(['message' => 'The memo does not exist'], 403);
        }

        $notice->update($request->all());
        return response()->json(['notice' => $notice, 'message' => 'The memo has been successfully updated'], 200);
    }

    // 공지 삭제(관리자)
    public function destroy(string $id)
    {
        $notice = $this->noticeModel->find($id);
        if (!$notice) {
            return response()->json(['message' => 'The memo does not exist'], 403);
        }

        $notice->delete();
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
