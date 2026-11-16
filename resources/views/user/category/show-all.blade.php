@extends('layouts.user.app')
@section('title', $title ?? 'Kho Danh Mục Tài Khoản Game')
@section('content')

@php
    $groupedCategories = [];
    if (isset($gameGroups)) {
        foreach($gameGroups as $gg) {
            $groupedCategories[trim($gg->name)] = collect([]);
        }
    }
    
    foreach($categories as $category) {
        if ($category->gameGroup) {
            $groupName = trim($category->gameGroup->name);
            if (!isset($groupedCategories[$groupName])) {
                $groupedCategories[$groupName] = collect([]);
            }
            $groupedCategories[$groupName]->push($category);
        } else {
            $groupName = $category->platform ? trim($category->platform) : 'Danh Mục Game';
            if (!isset($groupedCategories[$groupName])) {
                $groupedCategories[$groupName] = collect([]);
            }
            $groupedCategories[$groupName]->push($category);
        }
    }
@endphp

<div class="service-detail-wrapper" style="padding-top: 24px; padding-bottom: 60px;">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="gaming-breadcrumb" aria-label="Breadcrumb">
            <a href="/"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <span class="gaming-breadcrumb-sep">/</span>
            <span class="gaming-breadcrumb-current">Tất Cả Danh Mục Game</span>
        </nav>

        <!-- Header Banner -->
        <header class="service-header-banner" style="margin-bottom: 28px;">
            <div class="service-header-left">
                <div class="service-security-pill">
                    <i class="fa-solid fa-shield-halved"></i> Giao Dịch Tự Động 100% - Bảo Hành Uy Tín
                </div>
                <h1 class="service-header-title">KHO TÀI KHOẢN GAME CHÍNH HÃNG</h1>
                <p class="service-header-desc">
                    Danh mục tài khoản game cập nhật liên tục, giao dịch tự động và an toàn. Danh sách game hiển thị bên dưới lấy trực tiếp từ hệ thống.
                </p>
            </div>
        </header>

        @if ($categories->count() > 0)
            @foreach ($groupedCategories as $platform => $group)
                @if($group->count() > 0)
                    <section class="section gaming-group-section" id="categories-{{ Str::slug($platform) }}" style="padding: 20px 0 32px;">
                        <div class="section-header-gaming">
                            <h2 class="section-title-gaming">
                                <i class="fa-solid fa-gamepad" style="color: var(--primary-accent);"></i> {{ mb_strtoupper($platform, 'UTF-8') }}
                            </h2>
                            <a href="{{ route('category.group', ['slug' => Str::slug($platform)]) }}" class="section-link-more">
                                Xem tất cả <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i>
                            </a>
                        </div>

                        @php
                            $sortedGroup = $group->sortBy(function($item) {
                                $isRandom = ($item instanceof \App\Models\RandomCategory);
                                return ($isRandom ? 10000000 : 0) + ($item->price ?? 0);
                            });
                        @endphp
                        <div class="gaming-grid-5">
                            @foreach ($sortedGroup as $category)
                                @if ($category->active)
                                    <a href="{{ $category->url ?? route('category.index', ['slug' => $category->slug]) }}" class="gaming-card">
                                        <div class="gaming-card-media">
                                            @if(isset($category->price) && $category->price > 0)
                                                <span class="gaming-card-badge" style="color: var(--accent-amber);">
                                                    {{ number_format($category->price) }} đ
                                                </span>
                                            @else
                                                <span class="gaming-card-badge">
                                                    ACC VIP
                                                </span>
                                            @endif
                                            @if($category->thumbnail)
                                                <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" class="gaming-card-img" loading="lazy">
                                            @else
                                                <div style="display:flex;align-items:center;justify-content:center;height:100%;background:rgba(255,255,255,0.03);color:var(--text-muted);">
                                                    <i class="fa-solid fa-gamepad" style="font-size:2.5rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="gaming-card-body">
                                            <h3 class="gaming-card-title">{{ $category->name }}</h3>
                                            <div class="gaming-card-meta">
                                                <i class="fa-solid fa-box-archive"></i>
                                                <span>Còn: {{ number_format($category->allAccount) }}</span>
                                                <span style="margin: 0 4px;">•</span>
                                                <span>Đã bán: {{ number_format($category->soldCount) }}</span>
                                            </div>
                                            <div class="gaming-card-action">
                                                <div class="btn-card-action">
                                                    <i class="fa-solid fa-cart-shopping"></i> Mua Ngay
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach
        @else
            <div class="empty-state" style="text-align: center; padding: 80px 20px; width: 100%;">
                <span class="iconify" data-icon="bx:box" style="font-size: 48px; color: #94a3b8; margin-bottom: 12px; display: inline-block;"></span>
                <div style="color: #94a3b8; font-size: 0.95rem;">Chưa có tài khoản nào</div>
            </div>
        @endif
    </div>
</div>
@endsection
