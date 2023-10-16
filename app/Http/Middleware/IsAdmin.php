<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 관리자 확인
        if ($request->user() && $request->user()->is_admin) {
            return $next($request);
        }

        // 관리자가 아닌 경우 응답 반환
        return response()->json(['message' => 'You are not an administrator'], 403);
    }
}
