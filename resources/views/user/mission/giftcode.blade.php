@extends('layouts.user.app')

@section('title', 'Đổi Mã Giftcode - Nhận Tiền Thưởng')

@push('css')
<style>
    .giftcode-page {
        padding: 40px 0 80px 0;
        min-height: 80vh;
    }
    .giftcode-card {
        max-width: 650px;
        margin: 0 auto 40px auto;
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        text-align: center;
    }
    .giftcode-icon {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: var(--primary, #dc2626);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 16px auto;
    }
    .giftcode-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text-color, #111827);
        margin-bottom: 6px;
    }
    .giftcode-subtitle {
        color: var(--text-muted, #6b7280);
        font-size: 0.98rem;
        margin-bottom: 26px;
    }
    .giftcode-input-group {
        position: relative;
        margin-bottom: 20px;
    }
    .giftcode-input {
        width: 100%;
        padding: 14px 20px;
        font-size: 1.25rem;
        font-weight: 700;
        text-align: center;
        letter-spacing: 2px;
        text-transform: uppercase;
        border: 2px solid var(--border-color, #e5e7eb);
        border-radius: 14px;
        background: var(--bg-card, #ffffff);
        color: var(--text-color, #111827);
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .giftcode-input:focus {
        border-color: var(--primary, #dc2626);
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.15);
    }
    .giftcode-submit-btn {
        width: 100%;
        padding: 14px;
        border-radius: 14px;
        background: var(--primary, #dc2626);
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s ease, transform 0.15s ease;
        box-shadow: 0 4px 14px rgba(220, 38, 38, 0.3);
    }
    .giftcode-submit-btn:hover {
        background: #b91c1c;
        transform: translateY(-2px);
    }
    .giftcode-submit-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* History Table */
    .history-card {
        max-width: 820px;
        margin: 0 auto;
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 18px;
        padding: 24px;
    }
    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .history-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-color, #111827);
        margin: 0;
    }
    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }
    .custom-table th {
        background: rgba(0, 0, 0, 0.02);
        padding: 12px 16px;
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--text-muted, #6b7280);
        text-align: left;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
    }
    .custom-table td {
        padding: 14px 16px;
        font-size: 0.92rem;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        color: var(--text-color, #374151);
    }
</style>
@endpush

@section('content')
<div class="giftcode-page">
    <div class="container">
        <!-- Giftcode Input Card -->
        <div class="giftcode-card">
            <div class="giftcode-icon">
                <span class="iconify" data-icon="ant-design:gift-filled"></span>
            </div>
            <h1 class="giftcode-title">Nhập Mã Giftcode Đổi Thưởng</h1>
            <p class="giftcode-subtitle">Dán mã quà tặng nhận được sau khi hoàn thành nhiệm vụ để cộng tiền vào tài khoản ngay lập tức!</p>

            @guest
                <div style="background:#fffbeb; border:1px solid #fef3c7; border-radius:12px; padding:16px; margin-bottom:20px; color:#92400e; font-size:0.95rem;">
                    <strong>Thông báo:</strong> Bạn cần <a href="{{ route('login') }}" style="color:#dc2626; font-weight:700; text-decoration:underline;">Đăng nhập</a> để đổi mã và nhận tiền vào ví nhé!
                </div>
            @endguest

            <form id="giftcodeForm" onsubmit="handleRedeem(event)">
                @csrf
                <div class="giftcode-input-group">
                    <input type="text" 
                           id="giftcode" 
                           name="giftcode" 
                           class="giftcode-input" 
                           placeholder="NHẬP MÃ TẠI ĐÂY (VD: ABCD1234)" 
                           maxlength="20" 
                           required 
                           autofocus>
                </div>
                <button type="submit" id="btnRedeem" class="giftcode-submit-btn">
                    <span class="iconify" data-icon="ant-design:check-outlined"></span>
                    <span>Xác Nhận Đổi Thưởng</span>
                </button>
            </form>

            <div style="margin-top:20px; font-size:0.9rem; color:var(--text-muted, #6b7280);">
                <span>Chưa có mã?</span>
                <a href="{{ route('missions.index') }}" style="color:var(--primary, #dc2626); font-weight:700; margin-left:4px; text-decoration:none;">
                    Làm nhiệm vụ ngay để lấy mã
                </a>
            </div>
        </div>

        <!-- Recent History -->
        @auth
        <div class="history-card">
            <div class="history-header">
                <h3 class="history-title">Lịch sử nhận thưởng gần đây của bạn</h3>
                <a href="{{ route('missions.history') }}" style="color:var(--primary, #dc2626); font-size:0.9rem; font-weight:600; text-decoration:none;">
                    Xem tất cả &rarr;
                </a>
            </div>
            <div style="overflow-x:auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Mã Giftcode</th>
                            <th>Nhiệm vụ</th>
                            <th>Tiền thưởng</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentHistories as $h)
                            <tr>
                                <td>
                                    <span style="font-family:monospace; font-weight:700; color:#2563eb; background:#eff6ff; padding:3px 8px; border-radius:6px;">
                                        {{ $h->code }}
                                    </span>
                                </td>
                                <td>{{ $h->mission->title ?? 'Nhiệm vụ vượt link' }}</td>
                                <td>
                                    <strong style="color:#059669;">+{{ number_format($h->reward_amount) }}đ</strong>
                                </td>
                                <td style="color:#6b7280; font-size:0.85rem;">{{ $h->created_at->format('H:i d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding:30px; color:#9ca3af;">
                                    Bạn chưa thực hiện đổi mã nhận thưởng nào. Hãy làm nhiệm vụ để nhận tiền nhé!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endauth
    </div>
</div>

<script>
    function handleRedeem(e) {
        e.preventDefault();
        const codeInput = document.getElementById('giftcode');
        const btn = document.getElementById('btnRedeem');
        const code = codeInput.value.trim();

        if (!code) {
            Swal.fire({
                icon: 'warning',
                title: 'Thông báo',
                text: 'Vui lòng nhập mã Giftcode!',
                confirmButtonColor: '#dc2626'
            });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="iconify" data-icon="ant-design:loading-outlined" style="animation: spin 1s infinite linear;"></span> Đang kiểm tra...';

        fetch("{{ route('missions.redeem') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ giftcode: code })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<span class="iconify" data-icon="ant-design:check-outlined"></span> <span>Xác Nhận Đổi Thưởng</span>';

            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: data.msg,
                    confirmButtonColor: '#dc2626'
                }).then(() => {
                    location.reload();
                });
                codeInput.value = '';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Thất bại',
                    text: data.msg || 'Mã Giftcode không hợp lệ!',
                    confirmButtonColor: '#dc2626'
                });
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span class="iconify" data-icon="ant-design:check-outlined"></span> <span>Xác Nhận Đổi Thưởng</span>';
            Swal.fire({
                icon: 'error',
                title: 'Lỗi kết nối',
                text: 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau!',
                confirmButtonColor: '#dc2626'
            });
        });
    }
</script>
@endsection
