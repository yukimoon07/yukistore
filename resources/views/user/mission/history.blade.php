@extends('layouts.user.app')

@section('title', 'Lịch Sử Nhận Thưởng Nhiệm Vụ')

@push('css')
<style>
    .mission-history-page {
        padding: 40px 0 80px 0;
        min-height: 80vh;
    }
    .history-card {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }
    .history-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-color, #111827);
        margin-bottom: 20px;
    }
    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }
    .custom-table th {
        background: rgba(0, 0, 0, 0.02);
        padding: 14px 16px;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-muted, #6b7280);
        text-align: left;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
    }
    .custom-table td {
        padding: 16px;
        font-size: 0.95rem;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        color: var(--text-color, #374151);
    }
</style>
@endpush

@section('content')
<div class="mission-history-page">
    <div class="container">
        <div class="history-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
                <h1 class="history-title" style="margin:0;">Lịch Sử Hoàn Thành Nhiệm Vụ</h1>
                <a href="{{ route('missions.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:var(--primary, #dc2626); font-weight:700; text-decoration:none;">
                    <span class="iconify" data-icon="ant-design:plus-circle-outlined"></span>
                    <span>Làm thêm nhiệm vụ</span>
                </a>
            </div>

            <div style="overflow-x:auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã Giftcode</th>
                            <th>Nhiệm vụ</th>
                            <th>Tiền thưởng</th>
                            <th>Địa chỉ IP</th>
                            <th>Thời gian nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $h)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span style="font-family:monospace; font-weight:700; color:#2563eb; background:#eff6ff; padding:4px 10px; border-radius:6px;">
                                        {{ $h->code }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $h->mission->title ?? 'Nhiệm vụ vượt link' }}</strong>
                                </td>
                                <td>
                                    <span style="color:#059669; font-weight:800; font-size:1.05rem;">
                                        +{{ number_format($h->reward_amount) }} VNĐ
                                    </span>
                                </td>
                                <td style="color:#6b7280; font-size:0.85rem;">{{ $h->ip_address }}</td>
                                <td style="color:#6b7280; font-size:0.88rem;">{{ $h->created_at->format('H:i:s d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding:40px; color:#9ca3af;">
                                    Bạn chưa có lịch sử nhận thưởng nhiệm vụ nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:20px;">
                {{ $histories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
