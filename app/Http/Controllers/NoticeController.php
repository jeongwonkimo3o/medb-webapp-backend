<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notice;

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
        $notices = $this->noticeModel->all();
        return response()->json(['notices' => $notices, 'message' => 'The notices has been successfully retrieved'], 200);
    }

    // 공지 작성(관리자)
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $notice = $this->noticeModel->create($request->all());
        return response()->json($notice, 201);
    }

    // 특정 id의 공지 확인
    public function show(string $id)
    {
        $notice = $this->noticeModel->find($id);
        if (!$notice) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        return response()->json($notice);
    }

    // 공지 업데이트(관리자)
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $notice = $this->noticeModel->find($id);
        if (!$notice) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        $notice->update($request->all());
        return response()->json($notice);
    }

    // 공지 삭제(관리자)
    public function destroy(string $id)
    {
        $notice = $this->noticeModel->find($id);
        if (!$notice) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        $notice->delete();
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
