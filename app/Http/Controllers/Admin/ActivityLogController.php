<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Nhật ký hoạt động quản trị — chỉ admin cấp cao (role 'admin') được xem.
 */
class ActivityLogController extends Controller
{
    /**
     * Danh sách nhật ký, lọc theo hành động và người thực hiện.
     */
    public function index(Request $request)
    {
        $title = 'Nhật ký hoạt động';

        $query = AdminActivityLog::with('admin')->orderByDesc('id');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->input('admin_id'));
        }

        $logs = $query->paginate(50)->withQueryString();

        // Danh sách hành động đã từng ghi nhận, dùng cho bộ lọc
        $actions = AdminActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Danh sách admin để lọc theo người thực hiện
        $admins = User::whereIn('id', AdminActivityLog::query()->select('admin_id')->whereNotNull('admin_id'))
            ->orderBy('username')
            ->get(['id', 'username']);

        return view('admin.activity-log.index', compact('title', 'logs', 'actions', 'admins'));
    }
}
