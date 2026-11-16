@extends('layouts.user.app')

@section('title', $title . ' - Danh Sách Tài Khoản & Túi Mù')

@section('content')
    <!-- SEO JSON-LD Structured Data: BreadcrumbList -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Trang Chủ",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "{{ $title }}",
                "item": "{{ url()->current() }}"
            }
        ]
    }
    </script>

    <div class="service-detail-wrapper" style="padding-top: 24px;">
        <div class="container">
            <!-- Breadcrumb Navigation -->
            <nav class="gaming-breadcrumb" aria-label="Breadcrumb">
                <a href="/"><i class="fa-solid fa-house"></i> Trang chủ</a>
                <span class="gaming-breadcrumb-sep">/</span>
                <span class="gaming-breadcrumb-current">{{ $platform }}</span>
            </nav>

            <!-- Header Banner -->
            <header class="service-header-banner" style="margin-bottom: 28px;">
                <div class="service-header-left">
                    <div class="service-security-pill">
                        <i class="fa-solid fa-shield-halved"></i> Kho Tài Khoản Game Uy Tín
                    </div>
                    <h1 class="service-header-title">{{ mb_strtoupper($platform, 'UTF-8') }}</h1>
                    <p class="service-header-desc">
                        Danh sách tài khoản game chính hãng, acc trắng thông tin, acc REG và thử vận may túi mù tỉ lệ trúng skin cực cao.
                    </p>
                </div>
            </header>

            <div class="section-header-gaming">
                <h2 class="section-title-gaming">
                    <i class="fa-solid fa-gamepad"></i> Danh Mục {{ $platform }} ({{ $categories->where('active', 1)->count() }})
                </h2>
            </div>

            @php
                $sortedCategories = $categories->sortBy(function($item) {
                    $isRandom = ($item instanceof \App\Models\RandomCategory);
                    return ($isRandom ? 10000000 : 0) + ($item->price ?? 0);
                });
            @endphp
            <div class="gaming-grid-5">
                @forelse ($sortedCategories as $category)
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
                                <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" class="gaming-card-img" loading="lazy">
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
                                        <i class="fa-solid fa-eye"></i> Xem Ngay
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endif
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: var(--bg-card); border-radius: var(--radius-md);">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem; color: var(--accent-amber); margin-bottom: 12px;"></i>
                        <h2 style="font-size: 1.2rem; color: var(--text-primary); margin-bottom: 6px;">Chưa có danh mục nào</h2>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Hệ thống đang cập nhật thêm tài khoản mới, vui lòng quay lại sau!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
