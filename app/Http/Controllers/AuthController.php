<?php

namespace App\Http\Controllers;

use App\Mail\RegisterMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|max:30|unique:users,nickname',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // 만약, 유효성 검사가 유효하지 않을 경우
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 유효성 검사 통과했을 경우
        $user = User::create([
            'nickname' => $request->nickname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => Str::random(60),  // 랜덤한 토큰 생성
        ]);

        // 사용자에게 메일 보내기
        Mail::to($user->email)->send(new RegisterMail([
            'body' => url('/verify-email?token=' . $user->verification_token),  // 메일 내용에 인증 URL 추가
        ]));

        return response()->json(['user' => $user, 'message' => '회원가입 성공'], 201);
    }

    public function verifyEmail(Request $request)
    {
        // 요청에서 토큰을 가져옴
        $token = $request->get('token');

        // 토큰 없으면 오류 페이지로 리다이렉트
        if (!$token) {
            return redirect('/error-page')->with('message', '권한이 없습니다.');
        }

        // 데이터베이스에서 토큰과 일치하는 유저 찾음
        /** @var User|null */
        $user = User::where('verification_token', '=', $token)->first();

        // 해당 유저 없으면, 오류 페이지로 리다이렉트
        if (!$user) {
            return redirect('/error-page')->with('message', '유저 정보를 찾을 수 없습니다.');
        }

        // 해당 유저가 있지만 이메일이 아직 인증되지 않았뎌면
        if ($user && !$user->email_verified_at) {
            // 현재 시간으로 이메일 인증 시간을 설정하고 저장
            $user->email_verified_at = now();
            $user->save();

            // 로그인 페이지로 리다이렉트
            return redirect('/login-page')->with('message', '이메일 인증이 완료되었습니다.');
        } else { // 이미 이메일 인증이 완료됐을 경우, 로그인 페이지로 리다이렉트
            return redirect('/login-page')->with('message', '이미 이메일 인증이 완료되었습니다.');
        }
    }

    public function login(Request $request)
    {
        // 빈 값으로 전달되지 않도록 유효성 검사
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 만약, DB의 계정 정보와 일치하지 않을 경우, 401 Unauthorized 반환 및 메서드 종료
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => '아이디 또는 비밀번호가 틀렸습니다.'], 401);
        }

        // 만약, email_verified_at이 빈 값일 경우 인증이 되지 않은 이메일로 간주하고 로그인을 막아버림
        $user = auth()->user();
        if ($user->email_verified_at === null) {
            return response()->json(['message' => '인증되지 않은 이메일입니다.'], 401);
        }

        /** @var \App\Models\User $user **/
        $user = Auth::user();

        // 토큰 생성
        $tokenResult = $user->createToken('AccessToken');

        // 액세스 토큰과 유저 정보 반환
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        // 토큰 삭제
        $request->user()->token()->revoke();

        return response()->json(['message' => '로그아웃 성공'], 200);
    }
}
