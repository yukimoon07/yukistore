<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Chỉ cho phép role 'admin' (quản trị cấp cao) đi qua.
     * Dev và member sẽ bị từ chối.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Chỉ quản trị viên cấp cao mới có quyền thực hiện thao tác này.',
                ], 403);
            }

            abort(403, 'Chỉ quản trị viên cấp cao mới có quyền thực hiện thao tác này.');
        }

        return $next($request);
    }
}
