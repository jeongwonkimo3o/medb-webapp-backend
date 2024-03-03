<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    private $users;

    // 생성자
    public function __construct(User $user)
    {
        $this->users = $user;
        $this->middleware('auth:api')->except('index', 'show');
    }

    // 유저 목록 반환 - 관리자 페이지: 유저 리스트
    public function index()
    {
        $users = $this->users->all();
        return response()->json($users, 200);
    }

    // 유저 각자의 정보
    public function show(string $id)
    {

        $users = $this->users->find($id);

        if (!$users) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($users, 200);
    }

    // 유저 정보 수정
    public function update(Request $request, string $id)
    {
        // 현재 인증된 사용자의 정보만을 가져오도록 조건을 추가합니다.
        $user = User::where('user_id', $id)->where('user_id', Auth::id())->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized or User not found'], 403);
        }

        // 새 닉네임이 제공된 경우 유효성을 검사
        if ($request->has('new_nickname')) {
            $request->validate([
                'new_nickname' => 'required|string|max:30|unique:users,nickname',
            ]);
            $user->nickname = $request->input('new_nickname');
        }

        // 비밀번호 변경이 요청된 경우
        if ($request->has('current_password') || $request->has('new_password')) {
            $request->validate([
                'current_password' => 'required_with:new_password',
                'new_password' => 'required_with:current_password|string|min:8|confirmed',
                'new_password_confirmation' => 'required_with:new_password'
            ]);

            // 현재 비밀번호 확인
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return response()->json(['message' => 'The current password is incorrect'], 400);
            }

            // 새 비밀번호 설정
            $user->password = Hash::make($request->input('new_password'));
        }

        // 변경된 내용을 저장
        $user->save();

        return response()->json(['message' => 'User info successfully updated'], 200);
    }

    // 유저 정보 삭제
    public function destroy(Request $request, string $id)
    {
        $user = User::where('user_id', $id)->where('user_id', Auth::id())->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized or User not found'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User successfully deleted'], 200);
        
    }
}
