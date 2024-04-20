<?php

namespace App\Http\Controllers;

use App\Mail\RegisterMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail(User $user)
    {
        $details = [
            'title' => '[MEDB] 회원가입을 위해 메일을 인증해 주세요.',
            'body' => url('/verify-email?token=' . $user->email_verification_token),
        ];

        Mail::to($user->email)->send(new RegisterMail($details));

        return "이메일 전송 완료";
    }
}
