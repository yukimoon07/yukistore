@extends('layouts.user.app')

@section('title', 'Dịch Vụ Cày Thuê Game')

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
                "name": "Dịch Vụ Game",
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
                <span class="gaming-breadcrumb-current">Dịch vụ game cày thuê</span>
            </nav>

            <!-- Header Banner -->
            <header class="service-header-banner" style="margin-bottom: 28px;">
                <div class="service-header-left">
                    <div class="service-security-pill">
                        <i class="fa-solid fa-bolt"></i> Dịch Vụ Cày Thuê Uy Tín 100%
                    </div>
                    <h1 class="service-header-title">Dịch Vụ Cày Thuê Game Uy Tín</h1>
                    <p class="service-header-desc">
                        Chọn dịch vụ, điền thông tin tài khoản và theo dõi tiến độ đơn hàng trực tiếp trong hệ thống. Cam kết bảo mật tài khoản, xử lý bởi kỹ thuật viên có kinh nghiệm.
                    </p>
                </div>
            </header>

            <!-- Grid of Services -->
            <div class="section-header-gaming">
                <h2 class="section-title-gaming">
                    <i class="fa-solid fa-gamepad"></i> Tất Cả Dịch Vụ Cày Thuê ({{ $services->where('active', 1)->count() }})
                </h2>
            </div>

            <div class="gaming-grid-5">
                @forelse ($services as $service)
                    @php
                        $minPrice = $service->activePackages->min('price');
                    @endphp
                    <a href="{{ route('service.show', ['slug' => $service->slug]) }}" class="gaming-card">
                        <div class="gaming-card-media">
                            <span class="gaming-card-badge">
                                <i class="fa-solid fa-shield-halved"></i> 2FA SAFE
                            </span>
                            @if ($service->thumbnail)
                                <img src="{{ $service->thumbnail }}" alt="{{ $service->name }}" class="gaming-card-img" loading="lazy" width="240" height="135">
                            @else
                                <div class="spotlight-img-placeholder gaming-card-img" aria-hidden="true">
                                    <span class="iconify" data-icon="ant-design:gamepad-outlined"></span>
                                </div>
                            @endif
                        </div>
                        <div class="gaming-card-body">
                            <h3 class="gaming-card-title">{{ $service->name }}</h3>
                            <div class="gaming-card-meta">
                                @if ($minPrice)
                                    <span><i class="fa-solid fa-tag"></i> từ {{ number_format($minPrice, 0, ',', '.') }} đ</span>
                                @else
                                    <span><i class="fa-solid fa-hourglass-half"></i> Giá đang cập nhật</span>
                                @endif
                            </div>
                            <div class="gaming-card-meta">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>{{ number_format($service->orderCount) }} lượt thuê</span>
                            </div>
                            <div class="gaming-card-action">
                                @if ($minPrice)
                                    <div class="btn-card-action">
                                        <i class="fa-solid fa-bolt"></i> Thuê Ngay
                                    </div>
                                @else
                                    <div class="btn-card-action" style="opacity:.55; cursor: default;">
                                        <i class="fa-solid fa-circle-info"></i> Xem chi tiết
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: var(--bg-card); border-radius: var(--radius-md);">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem; color: var(--accent-amber); margin-bottom: 12px;"></i>
                        <h2 style="font-size: 1.2rem; color: var(--text-primary); margin-bottom: 6px;">Chưa có dịch vụ nào</h2>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Hệ thống đang cập nhật thêm dịch vụ mới, vui lòng quay lại sau!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
