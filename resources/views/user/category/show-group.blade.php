@extends('layouts.user.app')

@section('title', $title . ' - Danh sách tài khoản & túi mù')

@section('content')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Trang chủ", "item": "{{ url('/') }}" },
            { "@type": "ListItem", "position": 2, "name": @json($title), "item": "{{ url()->current() }}" }
        ]
    }
    </script>

    <section class="yk-page-hero">
        <div class="container">
            <nav class="yk-breadcrumb" aria-label="Breadcrumb">
                <a href="/"><i class="fa-solid fa-house" aria-hidden="true"></i> Trang chủ</a>
                <span class="yk-breadcrumb__sep">/</span>
                <span class="yk-breadcrumb__current">{{ $platform }}</span>
            </nav>

            <h1 class="yk-page-hero__title">{{ $platform }}</h1>
            <p class="yk-page-hero__desc">
                Tài khoản chính hãng, acc trắng thông tin và túi mù. Giá hiển thị là giá thấp nhất còn hàng.
            </p>

            <div class="yk-stat-row">
                <span class="yk-badge yk-badge--info">
                    {{ $categories->where('active', 1)->count() }} danh mục
                </span>
            </div>
        </div>
    </section>

    <section class="yk-section">
        <div class="container">
            @php
                $sortedCategories = $categories->sortBy(function ($item) {
                    $isRandom = ($item instanceof \App\Models\RandomCategory);
                    return ($isRandom ? 10000000 : 0) + ($item->price ?? 0);
                });
            @endphp

            <div class="yk-grid">
                @forelse ($sortedCategories as $category)
                    @if ($category->active)
                        <a href="{{ $category->url ?? route('category.index', ['slug' => $category->slug]) }}" class="yk-tile">
                            <div class="yk-tile__media">
                                @if($category->thumbnail)
                                    <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" loading="lazy" width="400" height="225">
                                @else
                                    <div class="yk-tile__placeholder" aria-hidden="true">
                                        <i class="fa-solid fa-gamepad"></i>
                                    </div>
                                @endif

                                @if(isset($category->price) && $category->price > 0)
                                    <span class="yk-tile__price">{{ number_format($category->price) }} đ</span>
                                @else
                                    <span class="yk-tile__badge">ACC VIP</span>
                                @endif
                            </div>

                            <div class="yk-tile__body">
                                <h3 class="yk-tile__title">{{ $category->name }}</h3>
                                <div class="yk-tile__meta">
                                    <span>Còn {{ number_format($category->allAccount) }}</span>
                                    <span class="dot" aria-hidden="true">·</span>
                                    <span>Đã bán {{ number_format($category->soldCount) }}</span>
                                </div>
                                <div class="yk-tile__cta">
                                    <span>Xem ngay</span>
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                    @endif
                @empty
                    <div class="yk-empty">
                        <div class="yk-empty__icon"><i class="fa-solid fa-box-open" aria-hidden="true"></i></div>
                        <h2 class="yk-empty__title">Chưa có danh mục nào</h2>
                        <p class="yk-empty__text">Hệ thống đang cập nhật thêm tài khoản mới, vui lòng quay lại sau.</p>
                        <a href="{{ route('category.show-all') }}" class="yk-btn yk-btn--ghost yk-btn--sm">Xem tất cả danh mục</a>
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
