<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chặn phiên đang hoạt động của tài khoản bị khóa (banned).
 * Áp cho nhóm route 'auth' của website — admin có middleware riêng.
 */
class EnsureUserNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->banned) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.');
        }

        return $next($request);
    }
}
