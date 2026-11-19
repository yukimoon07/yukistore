@extends('layouts.user.app')

@section('title', 'Dịch vụ cày thuê game')

@section('content')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Trang chủ", "item": "{{ url('/') }}" },
            { "@type": "ListItem", "position": 2, "name": "Dịch vụ game", "item": "{{ url()->current() }}" }
        ]
    }
    </script>

    <section class="yk-page-hero">
        <div class="container">
            <nav class="yk-breadcrumb" aria-label="Breadcrumb">
                <a href="/"><i class="fa-solid fa-house" aria-hidden="true"></i> Trang chủ</a>
                <span class="yk-breadcrumb__sep">/</span>
                <span class="yk-breadcrumb__current">Dịch vụ cày thuê</span>
            </nav>

            <h1 class="yk-page-hero__title">Dịch vụ cày thuê game</h1>
            <p class="yk-page-hero__desc">
                Chọn dịch vụ, điền thông tin tài khoản và theo dõi tiến độ đơn hàng trong hệ thống.
                Thông tin đăng nhập được mã hóa và xóa sau khi đơn hoàn tất.
            </p>

            <div class="yk-stat-row">
                <span class="yk-badge yk-badge--info">{{ $services->where('active', 1)->count() }} dịch vụ</span>
                <span class="yk-badge yk-badge--success"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Bảo mật 2FA</span>
            </div>
        </div>
    </section>

    <section class="yk-section">
        <div class="container">
            <div class="yk-grid">
                @forelse ($services as $service)
                    @php $minPrice = $service->activePackages->min('price'); @endphp
                    <a href="{{ route('service.show', ['slug' => $service->slug]) }}" class="yk-tile">
                        <div class="yk-tile__media">
                            @if ($service->thumbnail)
                                <img src="{{ $service->thumbnail }}" alt="{{ $service->name }}" loading="lazy" width="400" height="225">
                            @else
                                <div class="yk-tile__placeholder" aria-hidden="true">
                                    <i class="fa-solid fa-screwdriver-wrench"></i>
                                </div>
                            @endif

                            @if ($minPrice)
                                <span class="yk-tile__price">từ {{ number_format($minPrice) }} đ</span>
                            @else
                                <span class="yk-tile__badge">Giá đang cập nhật</span>
                            @endif
                        </div>

                        <div class="yk-tile__body">
                            <h2 class="yk-tile__title">{{ $service->name }}</h2>
                            <div class="yk-tile__meta">
                                <span>{{ number_format($service->orderCount) }} lượt thuê</span>
                            </div>
                            <div class="yk-tile__cta">
                                <span>{{ $minPrice ? 'Thuê ngay' : 'Xem chi tiết' }}</span>
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="yk-empty">
                        <div class="yk-empty__icon"><i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i></div>
                        <h2 class="yk-empty__title">Chưa có dịch vụ nào</h2>
                        <p class="yk-empty__text">Hệ thống đang cập nhật thêm dịch vụ mới, vui lòng quay lại sau.</p>
                        <a href="/" class="yk-btn yk-btn--ghost yk-btn--sm">Về trang chủ</a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@push('css')
<style>
    .yk-stat-row {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-2);
        margin-top: var(--space-4);
    }

    .yk-tile__placeholder {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: var(--yuki-text-faint);
        font-size: 1.75rem;
    }
</style>
@endpush
