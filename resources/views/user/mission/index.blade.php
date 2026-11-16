@extends('layouts.user.app')

@section('title', __('shop.mission_page_title') . ' - ' . config_get('site_name', 'Shop Game'))

@push('css')
<style>
    .mission-page {
        padding: 40px 0 80px 0;
        min-height: 80vh;
    }
    .mission-header {
        text-align: center;
        margin-bottom: 35px;
    }
    .mission-badge-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 18px;
        border-radius: 9999px;
        background: rgba(6, 182, 212, 0.12);
        border: 1px solid rgba(6, 182, 212, 0.25);
        color: var(--accent-cyan, #06b6d4);
        font-weight: 700;
        font-size: 0.88rem;
        margin-bottom: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .mission-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--text-primary, #f8fafc);
        margin-bottom: 10px;
        letter-spacing: -0.02em;
    }
    .mission-subtitle {
        color: var(--text-secondary, #94a3b8);
        font-size: 1.05rem;
        max-width: 650px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* 3-Step Process Stepper */
    .mission-process-guide {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        max-width: 900px;
        margin: 0 auto 35px auto;
    }
    @media (max-width: 768px) {
        .mission-process-guide { grid-template-columns: 1fr; }
    }
    .process-step-card {
        background: var(--bg-card, #151f32);
        border: 1px solid var(--border-card, #1e2c45);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .process-step-num {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #2563eb, #06b6d4);
        color: #fff;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .process-step-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-primary, #f8fafc);
        line-height: 1.35;
    }

    /* Action Nav Bar */
    .mission-nav-bar {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-bottom: 35px;
        flex-wrap: wrap;
    }
    .mission-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.92rem;
        text-decoration: none;
        transition: all 0.25s ease;
        border: 1px solid var(--border-card, #1e2c45);
        background: var(--bg-card, #151f32);
        color: var(--text-secondary, #cbd5e1);
    }
    .mission-nav-btn:hover, .mission-nav-btn.active {
        background: linear-gradient(135deg, #2563eb, #06b6d4);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 14px rgba(6, 182, 212, 0.3);
    }

    /* Stats Grid */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
        margin-bottom: 35px;
    }
    .stat-card {
        background: var(--bg-card, #151f32);
        border: 1px solid var(--border-card, #1e2c45);
        border-radius: 14px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.25);
    }
    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary, #f8fafc);
        line-height: 1.2;
    }
    .stat-label {
        font-size: 0.88rem;
        color: var(--text-muted, #94a3b8);
    }

    /* Missions Grid */
    .mission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 22px;
    }
    .mission-card {
        background: var(--bg-card, #151f32);
        border: 1px solid var(--border-card, #1e2c45);
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }
    .mission-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(6, 182, 212, 0.2);
        border-color: var(--accent-cyan, #06b6d4);
    }
    .mission-card-body {
        padding: 22px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .mission-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
    }
    .mission-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(6, 182, 212, 0.12);
        color: var(--accent-cyan, #06b6d4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        border: 1px solid rgba(6, 182, 212, 0.2);
    }
    .mission-tag {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .mission-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary, #f8fafc);
        margin-bottom: 8px;
        line-height: 1.4;
    }
    .mission-card-desc {
        font-size: 0.88rem;
        color: var(--text-secondary, #94a3b8);
        margin-bottom: 16px;
        flex-grow: 1;
        line-height: 1.5;
    }
    .mission-reward-box {
        background: rgba(245, 158, 11, 0.08);
        border: 1px dashed rgba(245, 158, 11, 0.3);
        border-radius: 12px;
        padding: 10px 14px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .mission-reward-label {
        font-size: 0.82rem;
        color: #94a3b8;
    }
    .mission-reward-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--accent-amber, #f59e0b);
    }
    .mission-btn {
        width: 100%;
        padding: 11px 16px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.92rem;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        background: linear-gradient(135deg, #2563eb, #06b6d4);
        color: #ffffff;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.25);
    }
    .mission-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(6, 182, 212, 0.4);
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="mission-page">
    <div class="container">
        <!-- Header -->
        <div class="mission-header">
            <div class="mission-badge-title">
                <span class="iconify" data-icon="ant-design:fire-filled"></span>
                <span>{{ __('shop.mission_page_title') }}</span>
            </div>
            <h1 class="mission-title">{{ __('shop.mission_page_title') }}</h1>
            <p class="mission-subtitle">{{ __('shop.mission_page_subtitle') }}</p>
        </div>

        <!-- 3-Step Process Stepper -->
        <div class="mission-process-guide">
            <div class="process-step-card">
                <div class="process-step-num">1</div>
                <div class="process-step-text">{{ __('shop.mission_step_1') }}</div>
            </div>
            <div class="process-step-card">
                <div class="process-step-num">2</div>
                <div class="process-step-text">{{ __('shop.mission_step_2') }}</div>
            </div>
            <div class="process-step-card">
                <div class="process-step-num">3</div>
                <div class="process-step-text">{{ __('shop.mission_step_3') }}</div>
            </div>
        </div>

        <!-- Quick Nav Buttons -->
        <div class="mission-nav-bar">
            <a href="{{ route('missions.index') }}" class="mission-nav-btn active">
                <span class="iconify" data-icon="ant-design:unordered-list-outlined"></span> {{ __('shop.mission_nav_list') }}
            </a>
            <a href="{{ route('missions.giftcode') }}" class="mission-nav-btn">
                <span class="iconify" data-icon="ant-design:gift-outlined"></span> {{ __('shop.mission_nav_giftcode') }}
            </a>
            @auth
            <a href="{{ route('missions.history') }}" class="mission-nav-btn">
                <span class="iconify" data-icon="ant-design:history-outlined"></span> {{ __('shop.mission_nav_history') }}
            </a>
            @endauth
        </div>

        <!-- Stats Bar -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(37, 99, 235, 0.15); color:#38bdf8;">
                    <span class="iconify" data-icon="ant-design:check-circle-outlined"></span>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($stats['total_completed']) }}</div>
                    <div class="stat-label">Nhiệm vụ bạn đã hoàn thành</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(16, 185, 129, 0.15); color:#34d399;">
                    <span class="iconify" data-icon="ant-design:wallet-outlined"></span>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($stats['total_earned']) }}đ</div>
                    <div class="stat-label">Tổng tiền bạn đã kiếm được</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(245, 158, 11, 0.15); color:#fbbf24;">
                    <span class="iconify" data-icon="ant-design:thunderbolt-outlined"></span>
                </div>
                <div>
                    <div class="stat-value">{{ $missions->count() }} Đơn</div>
                    <div class="stat-label">Nhiệm vụ đang sẵn sàng làm</div>
                </div>
            </div>
        </div>

        <!-- Missions List Grid -->
        <div class="mission-grid">
            @forelse($missions as $m)
                <div class="mission-card">
                    <div class="mission-card-body">
                        <div class="mission-card-top">
                            <div class="mission-icon-box">
                                <span class="iconify" data-icon="ant-design:link-outlined"></span>
                            </div>
                            <span class="mission-tag">Còn Lượt</span>
                        </div>
                        <h3 class="mission-card-title">{{ $m->title }}</h3>
                        <p class="mission-card-desc">
                            {{ $m->description ?: 'Làm theo hướng dẫn, vượt link đích hoặc review địa điểm để nhận mã quà tặng.' }}
                        </p>
                        <div class="mission-reward-box">
                            <span class="mission-reward-label">{{ __('shop.mission_reward_text') }}:</span>
                            <span class="mission-reward-value">
                                @if($m->reward_min == $m->reward_max)
                                    {{ number_format($m->reward_min) }}đ
                                @else
                                    {{ number_format($m->reward_min) }}đ - {{ number_format($m->reward_max) }}đ
                                @endif
                            </span>
                        </div>
                        <a href="{{ route('missions.show', $m->id) }}" class="mission-btn">
                            <span>{{ __('shop.mission_start_btn') }}</span>
                            <span class="iconify" data-icon="ant-design:arrow-right-outlined"></span>
                        </a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--bg-card, #151f32); border-radius: 16px; border: 1px dashed var(--border-card, #1e2c45);">
                    <div style="font-size: 48px; color: #64748b; margin-bottom: 12px;">
                        <span class="iconify" data-icon="ant-design:inbox-outlined"></span>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #f8fafc; margin-bottom: 6px;">Hiện tại chưa có nhiệm vụ mới</h3>
                    <p style="color: #94a3b8;">Các nhiệm vụ mới sẽ được quản trị viên cập nhật liên tục mỗi ngày, bạn vui lòng quay lại sau nhé!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
