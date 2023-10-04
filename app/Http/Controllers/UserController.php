<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{

    private $users;

    // 생성자
    public function __construct()
    {
        $this->users = new User();
    }

    // 유저 목록 반환 - 관리자 페이지: 유저 리스트
    public function index()
    {
        $users = $this->users->all();
        return response()->json($users, 200);
    }

    // 유저 생성
    public function store(Request $request)
    {
        //
    }

    // 유저 각자의 정보
    public function show(string $id)
    {

        $users = $this->users->find($id);

        if (!$users) {
            return response()->json(['message' => '사용자를 찾을 수 없습니다.'], 404);
        }

        return response()->json($users, 200);
    
        

    }

    // 유저 정보 수정
    public function update(Request $request, string $id)
    {
        //
    }

    // 유저 정보 삭제
    public function destroy(string $id)
    {
        //
    }
}
