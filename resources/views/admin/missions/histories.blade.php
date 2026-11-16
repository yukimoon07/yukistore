@extends('layouts.admin.app')

@section('content')
<div class="page-header mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">{{ $title }}</h2>
            <div class="text-muted mt-1">Lịch sử người dùng đã hoàn thành nhiệm vụ và nhận tiền thưởng</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.missions.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Danh sách nhiệm vụ
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Người dùng</th>
                    <th>Nhiệm vụ</th>
                    <th>Mã Giftcode</th>
                    <th>Tiền thưởng</th>
                    <th>Địa chỉ IP</th>
                    <th>Thời gian nhận</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $h)
                    <tr>
                        <td>{{ $h->id }}</td>
                        <td>
                            @if($h->user)
                                <strong>{{ $h->user->username }}</strong>
                                <div class="text-muted small">{{ $h->user->email }}</div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $h->mission->title ?? 'Nhiệm vụ vượt link' }}</td>
                        <td>
                            <span class="badge bg-blue-subtle text-blue font-monospace">{{ $h->code }}</span>
                        </td>
                        <td>
                            <strong class="text-success">+{{ number_format($h->reward_amount) }}đ</strong>
                        </td>
                        <td>{{ $h->ip_address }}</td>
                        <td>{{ $h->created_at->format('H:i:s d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Chưa có lịch sử làm nhiệm vụ nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $histories->links() }}
    </div>
</div>
@endsection
