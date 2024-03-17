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
    public function update(Request $request)
    {
        // 현재 인증된 사용자를 찾음
        $user = User::where('id', Auth::id())->first();

        // 사용자가 없는 경우의 처리는 동일
        if (!$user) {
            return response()->json(['message' => 'Unauthorized or User not found'], 403);
        }

        $newNickname = $request->input('newNickname');
        $currentPassword = $request->input('currentPassword');
        $newPassword = $request->input('newPassword');
        $confirmPassword = $request->input('confirmPassword');

        if ($newNickname !== null) {
            $request->validate([
                'newNickname' => 'required|string|max:30|unique:users,nickname',
            ]);
            $user->nickname = $newNickname;
        }

        // 비밀번호 변경 로직은 동일
        if ($currentPassword !== null || $newPassword !== null) {
            $request->validate([
                'currentPassword' => 'required_with:newPassword',
                'newPassword' => 'required_with:currentPassword|string|min:8|confirmed',
                'confirmPassword' => 'required_with:newPassword'
            ]);

            if (!Hash::check($currentPassword, $user->password)) {
                return response()->json(['message' => 'The current password is incorrect'], 400);
            }

            $user->password = Hash::make($newPassword);
        }

        $user->save();

        // 수정된 사용자 정보를 다시 불러옴
        $updatedUser = User::find($user->id);

        return response()->json(['message' => 'User info successfully updated', 'user' => $updatedUser], 200);
    }


    // 유저 정보 삭제
    public function destroy(Request $request)
    {
        // 현재 인증된 사용자를 찾음
        $user = User::find(Auth::id());

        // 사용자가 없는 경우의 처리
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User successfully deleted'], 200);
    }
}
