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

    // 유저 목록 반환
    public function index()
    {
        // 현재 인증된 사용자를 찾음
        $user = User::find(Auth::id());

        // 사용자가 없는 경우의 처리
        if (!$user) {
            return response()->json(['message' => '사용자 정보가 없습니다.'], 403);
        }

        // 사용자가 관리자가 아닌 경우의 처리
        if (!$user->is_admin) {
            return response()->json(['message' => '관리자가 아닙니다.'], 403);
        }

        $users = $this->users->all();

        return response()->json($users, 200);
    }

    // 유저 각자의 정보
    public function show(string $id)
    {

        $users = $this->users->find($id);

        if (!$users) {
            return response()->json(['message' => '유저를 찾을 수 없습니다.'], 404);
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
            return response()->json(['message' => '사용자의 정보를 찾을 수 없습니다.'], 403);
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
                return response()->json(['message' => '비밀번호가 틀렸습니다.'], 400);
            }

            $user->password = Hash::make($newPassword);
        }

        $user->save();

        // 수정된 사용자 정보를 다시 불러옴
        $updatedUser = User::find($user->id);

        return response()->json(['message' => '유저 정보가 업데이트 되었습니다.', 'user' => $updatedUser], 200);
    }


    // 유저 정보 삭제
    public function destroy($id) // 요청으로부터 직접 id를 받음
    {
        // 탈퇴시킬 사용자를 찾음
        $userToBeDeleted = User::find($id);

        // 사용자가 없는 경우의 처리
        if (!$userToBeDeleted) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // 현재 인증된 사용자를 가져옴
        $currentUser = Auth::user();

        // 본인 확인 또는 현재 사용자가 관리자인지 확인
        if ($currentUser->id === $userToBeDeleted->id || $currentUser->is_admin == 1) {
            $userToBeDeleted->delete();
            return response()->json(['message' => '탈퇴가 완료되었습니다.'], 200);
        } else {
            // 본인이 아니고, 현재 사용자도 관리자가 아닌 경우
            return response()->json(['message' => '유효하지 않은 접근입니다.'], 403);
        }
    }
}
