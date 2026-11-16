@extends('layouts.admin.app')

@section('content')
<div class="page-header mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">{{ $title }}</h2>
            <div class="text-muted mt-1">Quản lý nhiệm vụ vượt link, review maps, traffic user</div>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.missions.histories') }}" class="btn btn-outline-primary">
                    <i class="ti ti-history me-1"></i> Lịch sử làm nhiệm vụ
                </a>
                <a href="{{ route('admin.missions.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Thêm nhiệm vụ mới
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row mb-3">
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3">
            <div class="d-flex align-items-center">
                <span class="avatar avatar-md bg-primary-subtle text-primary me-3">
                    <i class="ti ti-list-check fs-2"></i>
                </span>
                <div>
                    <h4 class="m-0">{{ $totalMissions }}</h4>
                    <small class="text-muted">Tổng số nhiệm vụ</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3">
            <div class="d-flex align-items-center">
                <span class="avatar avatar-md bg-success-subtle text-success me-3">
                    <i class="ti ti-circle-check fs-2"></i>
                </span>
                <div>
                    <h4 class="m-0">{{ number_format($totalCompleted) }}</h4>
                    <small class="text-muted">Lượt hoàn thành</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3">
            <div class="d-flex align-items-center">
                <span class="avatar avatar-md bg-warning-subtle text-warning me-3">
                    <i class="ti ti-coin fs-2"></i>
                </span>
                <div>
                    <h4 class="m-0">{{ number_format($totalPaid) }}đ</h4>
                    <small class="text-muted">Tổng tiền thưởng đã phát</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên nhiệm vụ</th>
                    <th>Loại</th>
                    <th>Mức thưởng</th>
                    <th>Giãn cách</th>
                    <th>Đã làm</th>
                    <th>Trạng thái</th>
                    <th class="w-1">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($missions as $m)
                    <tr>
                        <td>{{ $m->id }}</td>
                        <td>
                            <strong>{{ $m->title }}</strong>
                            <div class="text-muted small text-truncate" style="max-width: 280px;">
                                {{ $m->target_url }}
                            </div>
                        </td>
                        <td><span class="badge bg-blue-subtle text-blue">{{ strtoupper($m->service_type) }}</span></td>
                        <td>
                            <strong class="text-success">
                                {{ number_format($m->reward_min) }}đ - {{ number_format($m->reward_max) }}đ
                            </strong>
                        </td>
                        <td>{{ $m->cooldown_seconds }}s</td>
                        <td><strong>{{ number_format($m->total_completed) }}</strong> lượt</td>
                        <td>
                            @if($m->status)
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Tạm tắt</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.missions.edit', $m->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-edit"></i> Sửa
                                </a>
                                <form action="{{ route('admin.missions.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhiệm vụ này không?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Chưa có nhiệm vụ nào. Nhấn "Thêm nhiệm vụ mới" để tạo!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $missions->links() }}
    </div>
</div>
@endsection
