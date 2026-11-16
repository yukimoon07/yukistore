<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (Auth::user()->canAccessAdminPanel()) {
            return $next($request);
        }

        // Đã đăng nhập nhưng là member → không được vào admin panel
        return redirect('/')->with('error', 'Bạn không có quyền truy cập trang quản trị!');
    }
}
