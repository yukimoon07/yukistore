@extends('layouts.user.app')

@section('title', 'Chi tiết nhiệm vụ - ' . $mission->title)

@push('css')
<style>
    .mission-detail-page {
        padding: 40px 0 80px 0;
        min-height: 80vh;
    }
    .mission-detail-box {
        max-width: 820px;
        margin: 0 auto;
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }
    .detail-header {
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        padding-bottom: 24px;
        margin-bottom: 24px;
    }
    .detail-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text-color, #111827);
        margin-bottom: 12px;
    }
    .detail-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.95rem;
        color: var(--text-muted, #6b7280);
    }
    .meta-item strong {
        color: var(--text-color, #111827);
    }
    .meta-highlight {
        color: var(--primary, #dc2626) !important;
        font-weight: 800 !important;
        font-size: 1.1rem;
    }

    /* Steps */
    .steps-container {
        margin-bottom: 30px;
    }
    .step-item {
        display: flex;
        gap: 16px;
        margin-bottom: 20px;
        align-items: flex-start;
    }
    .step-number {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--primary, #dc2626);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .step-content {
        background: rgba(0, 0, 0, 0.02);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 12px;
        padding: 16px;
        flex-grow: 1;
    }
    .step-content h4 {
        margin: 0 0 6px 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-color, #111827);
    }
    .step-content p {
        margin: 0;
        font-size: 0.92rem;
        color: var(--text-muted, #4b5563);
        line-height: 1.5;
    }

    /* Action Box */
    .action-box {
        text-align: center;
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.05) 0%, rgba(220, 38, 38, 0.02) 100%);
        border: 2px dashed rgba(220, 38, 38, 0.2);
        border-radius: 16px;
        padding: 28px 20px;
        margin-top: 24px;
    }
    .action-countdown {
        font-size: 1.1rem;
        font-weight: 600;
        color: #d97706;
        margin-bottom: 16px;
    }
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 32px;
        border-radius: 14px;
        background: var(--primary, #dc2626);
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(220, 38, 38, 0.35);
        transition: all 0.25s ease;
        border: none;
        cursor: pointer;
    }
    .action-btn:hover {
        background: #b91c1c;
        transform: translateY(-2px);
        color: #ffffff;
    }
    .action-btn.disabled {
        background: #9ca3af;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }
    .action-secondary {
        margin-top: 14px;
        font-size: 0.95rem;
    }
</style>
@endpush

@section('content')
<div class="mission-detail-page">
    <div class="container">
        <div class="mission-detail-box">
            <!-- Header -->
            <div class="detail-header">
                <a href="{{ route('missions.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:var(--text-muted, #6b7280); text-decoration:none; margin-bottom:12px; font-weight:600; font-size:0.9rem;">
                    <span class="iconify" data-icon="ant-design:arrow-left-outlined"></span>
                    <span>Quay lại danh sách nhiệm vụ</span>
                </a>
                <h1 class="detail-title">{{ $mission->title }}</h1>
                <div class="detail-meta">
                    <div class="meta-item">
                        <span>Phần thưởng:</span>
                        <strong class="meta-highlight">
                            @if($mission->reward_min == $mission->reward_max)
                                {{ number_format($mission->reward_min) }}đ
                            @else
                                {{ number_format($mission->reward_min) }}đ - {{ number_format($mission->reward_max) }}đ
                            @endif
                        </strong>
                    </div>
                    <div class="meta-item">
                        <span class="iconify" data-icon="ant-design:clock-circle-outlined"></span>
                        <span>Giãn cách: <strong>{{ $mission->cooldown_seconds }} giây</strong></span>
                    </div>
                    <div class="meta-item">
                        <span class="iconify" data-icon="ant-design:check-circle-outlined"></span>
                        <span>Đã hoàn thành: <strong>{{ number_format($mission->total_completed) }} lượt</strong></span>
                    </div>
                </div>
            </div>

            <!-- Steps -->
            <div class="steps-container">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-color, #111827); margin-bottom: 18px;">
                    Các bước thực hiện nhiệm vụ:
                </h3>

                @if($mission->guide_steps)
                    {!! \App\Support\HtmlSanitizer::clean($mission->guide_steps) !!}
                @else
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Bấm nút "Làm Nhiệm Vụ Ngay" bên dưới</h4>
                            <p>Hệ thống sẽ chuyển hướng bạn đến liên kết của nhiệm vụ (trang tìm kiếm hoặc trang đối tác).</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Thực hiện theo hướng dẫn trên màn hình đích</h4>
                            <p>Kéo xuống chân trang, chờ thời gian đếm ngược kết thúc để hệ thống cấp mã <strong>Giftcode</strong> bí mật gồm 8 ký tự.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Sao chép mã Giftcode và đổi tiền</h4>
                            <p>Quay lại website, truy cập mục <strong>"Đổi Mã Giftcode"</strong> và dán mã vào để nhận tiền thưởng vào ví ngay lập tức!</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Box -->
            <div class="action-box">
                <div id="countdownMsg" class="action-countdown" style="{{ $cooldownRemaining > 0 ? '' : 'display:none;' }}">
                    <span class="iconify" data-icon="ant-design:hourglass-outlined"></span>
                    <span>Bạn vui lòng đợi <span id="secondsLeft">{{ $cooldownRemaining }}</span> giây nữa trước khi tiếp tục làm nhiệm vụ này.</span>
                </div>

                <a href="{{ route('missions.start', $mission->id) }}" 
                   target="_blank" 
                   id="btnStartMission" 
                   class="action-btn {{ $cooldownRemaining > 0 ? 'disabled' : '' }}"
                   onclick="handleStartClick(event)">
                    <span class="iconify" data-icon="ant-design:rocket-filled"></span>
                    <span>Làm Nhiệm Vụ Ngay</span>
                </a>

                <div class="action-secondary">
                    <span>Đã có mã quà tặng?</span>
                    <a href="{{ route('missions.giftcode') }}" style="color:var(--primary, #dc2626); font-weight:700; text-decoration:underline; margin-left:4px;">
                        Đổi Quà Nhận Tiền Ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let cooldown = {{ $cooldownRemaining }};
    const btn = document.getElementById('btnStartMission');
    const msg = document.getElementById('countdownMsg');
    const sec = document.getElementById('secondsLeft');

    if (cooldown > 0) {
        const timer = setInterval(() => {
            cooldown--;
            if (sec) sec.innerText = cooldown;
            if (cooldown <= 0) {
                clearInterval(timer);
                if (btn) btn.classList.remove('disabled');
                if (msg) msg.style.display = 'none';
            }
        }, 1000);
    }

    function handleStartClick(e) {
        if (cooldown > 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Vui lòng chờ!',
                text: 'Bạn cần đợi hết thời gian đếm ngược để làm tiếp nhiệm vụ này.',
                confirmButtonColor: '#dc2626'
            });
            return false;
        }
    }
</script>
@endsection
