<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Phân quyền quản trị — chỉ admin cấp cao (role 'admin') được dùng.
 * Cho phép nâng/hạ quyền giữa member / dev / admin.
 */
class RoleController extends Controller
{
    /**
     * Danh sách tài khoản quản trị (admin + dev) và form cấp quyền.
     */
    public function index()
    {
        $title = 'Phân quyền quản trị';

        // Danh sách những người đang có quyền vào admin panel
        $admins = User::whereIn('role', ['admin', 'dev'])
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 WHEN role = 'dev' THEN 1 ELSE 2 END")
            ->orderBy('id')
            ->get();

        // Dùng cho ô select "chọn người dùng" trong form cấp quyền
        $users = User::orderBy('id')->get(['id', 'username', 'email', 'role']);

        return view('admin.roles.index', compact('title', 'admins', 'users'));
    }

    /**
     * Cấp / thu quyền cho một người dùng.
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,dev,admin',
        ], [
            'user_id.required' => 'Vui lòng chọn người dùng.',
            'user_id.exists' => 'Người dùng không tồn tại.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'role.in' => 'Vai trò không hợp lệ.',
        ]);

        // Không cho admin tự đổi quyền của chính mình — tránh tự hạ quyền và mất quyền quản trị
        if ((int) $validated['user_id'] === (int) auth()->id()) {
            return back()->with('error', 'Bạn không thể tự thay đổi quyền của chính mình.');
        }

        $user = User::findOrFail($validated['user_id']);
        $oldRole = $user->role;
        $newRole = $validated['role'];

        if ($oldRole === $newRole) {
            return back()->with('success', "Tài khoản {$user->username} đã có vai trò này, không có gì thay đổi.");
        }

        $user->role = $newRole;
        $user->save();

        // Ghi nhật ký hoạt động quản trị
        AdminActivityLog::record('role_updated', $user, ['role' => $oldRole], ['role' => $newRole]);

        $labels = [
            'member' => 'Thành viên',
            'dev' => 'Dev',
            'admin' => 'Quản trị viên',
        ];
        $oldLabel = $labels[$oldRole] ?? $oldRole;
        $newLabel = $labels[$newRole] ?? $newRole;

        return back()->with('success', "Đã đổi quyền của {$user->username} từ {$oldLabel} sang {$newLabel}.");
    }
}
