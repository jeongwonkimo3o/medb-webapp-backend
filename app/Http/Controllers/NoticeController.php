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

    // 공지 작성(관리자)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $notice = $this->noticeModel;
        $notice->fill($request->all());
        $notice->user_id = Auth::id();
        $notice->save();

        return response()->json(['notice' => $notice, 'message' => 'The notice has been successfully created'], 201);
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
