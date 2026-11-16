@extends('layouts.admin.app')

@section('title', $title)

@section('content')
    <div>
        <div>
            <div class="page-header">
                <div class="page-block mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">{{ $title }}</h2>
                                <p class="text-muted">Lịch sử thao tác của quản trị viên — dữ liệu nhạy cảm đã được che tự động</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body pb-0">
                    <form action="{{ route('admin.activity-log.index') }}" method="GET" class="row g-2 align-items-end mb-3">
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label small text-muted mb-1">Hành động</label>
                            <select name="action" class="form-select form-select-sm">
                                <option value="">-- Tất cả --</option>
                                @foreach($actions as $a)
                                    <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label small text-muted mb-1">Quản trị viên</label>
                            <select name="admin_id" class="form-select form-select-sm">
                                <option value="">-- Tất cả --</option>
                                @foreach($admins as $adm)
                                    <option value="{{ $adm->id }}" {{ (string) request('admin_id') === (string) $adm->id ? 'selected' : '' }}>{{ $adm->username }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="ti ti-search me-1"></i> Lọc
                            </button>
                            <a href="{{ route('admin.activity-log.index') }}" class="btn btn-sm btn-light w-100">Bỏ lọc</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="text-uppercase small">Thời gian</th>
                                <th class="text-uppercase small">Quản trị viên</th>
                                <th class="text-uppercase small">Hành động</th>
                                <th class="text-uppercase small">Đối tượng</th>
                                <th class="text-uppercase small">IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td class="text-nowrap text-muted small">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '—' }}</td>
                                    <td class="small">
                                        @if($log->admin)
                                            <span class="fw-medium">{{ $log->admin->username }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $actionColors = [
                                                'login' => 'bg-light-primary text-primary',
                                                'login_failed' => 'bg-light-danger text-danger',
                                                'logout' => 'bg-light-secondary text-secondary',
                                                'role_updated' => 'bg-light-success text-success',
                                                'settings_updated' => 'bg-light-warning text-warning',
                                                '2fa_failed' => 'bg-light-danger text-danger',
                                            ];
                                            $cls = $actionColors[$log->action] ?? 'bg-light-secondary text-secondary';
                                        @endphp
                                        <span class="badge {{ $cls }} px-2 py-1">{{ $log->action }}</span>
                                    </td>
                                    <td class="small text-muted">
                                        @if($log->target_type)
                                            {{ class_basename($log->target_type) }} #{{ $log->target_id }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $log->ip }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="ti ti-history fs-1 d-block mb-2"></i>
                                        Chưa có nhật ký nào.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="d-flex justify-content-end p-3 border-top">
                        {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
