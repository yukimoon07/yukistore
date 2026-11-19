@extends('layouts.user.app')
@section('title', $title ?? 'Kho danh mục tài khoản game')
@section('content')

@php
    $groupedCategories = [];
    if (isset($gameGroups)) {
        foreach ($gameGroups as $gg) {
            $groupedCategories[trim($gg->name)] = collect([]);
        }
    }

    foreach ($categories as $category) {
        $groupName = $category->gameGroup
            ? trim($category->gameGroup->name)
            : ($category->platform ? trim($category->platform) : 'Danh mục khác');

        if (!isset($groupedCategories[$groupName])) {
            $groupedCategories[$groupName] = collect([]);
        }
        $groupedCategories[$groupName]->push($category);
    }

    $totalCategories = $categories->where('active', 1)->count();
@endphp

<section class="yk-page-hero">
    <div class="container">
        <nav class="yk-breadcrumb" aria-label="Breadcrumb">
            <a href="/"><i class="fa-solid fa-house" aria-hidden="true"></i> Trang chủ</a>
            <span class="yk-breadcrumb__sep">/</span>
            <span class="yk-breadcrumb__current">Tất cả danh mục</span>
        </nav>

        <h1 class="yk-page-hero__title">Kho tài khoản game</h1>
        <p class="yk-page-hero__desc">
            Danh mục tài khoản cập nhật liên tục, giao dịch tự động. Giá trên mỗi thẻ là giá thấp nhất
            còn hàng trong danh mục tại thời điểm hiện tại.
        </p>

        <div class="yk-stat-row">
            <span class="yk-badge yk-badge--info">{{ number_format($totalCategories) }} danh mục</span>
            <span class="yk-badge yk-badge--success"><i class="fa-solid fa-bolt" aria-hidden="true"></i> Giao dịch tự động</span>
        </div>
    </div>
</section>

<section class="yk-section">
    <div class="container">
        @if ($categories->count() > 0)
            @foreach ($groupedCategories as $platform => $group)
                @if($group->count() > 0)
                    @php
                        $sortedGroup = $group->sortBy(function ($item) {
                            $isRandom = ($item instanceof \App\Models\RandomCategory);
                            return ($isRandom ? 10000000 : 0) + ($item->price ?? 0);
                        });
                    @endphp

                    <div class="yk-group" id="categories-{{ \Illuminate\Support\Str::slug($platform) }}">
                        <div class="yk-section-head">
                            <h2 class="yk-section-title">{{ mb_strtoupper($platform, 'UTF-8') }}</h2>
                            <a href="{{ route('category.group', ['slug' => \Illuminate\Support\Str::slug($platform)]) }}" class="yk-section-link">
                                Xem tất cả <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>

                        <div class="yk-grid">
                            @foreach ($sortedGroup as $category)
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
                                                <span>Mua ngay</span>
                                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="yk-empty">
                <div class="yk-empty__icon"><i class="fa-solid fa-box-open" aria-hidden="true"></i></div>
                <h2 class="yk-empty__title">Chưa có danh mục nào</h2>
                <p class="yk-empty__text">Hệ thống đang cập nhật tài khoản mới. Vui lòng quay lại sau.</p>
                <a href="/" class="yk-btn yk-btn--ghost yk-btn--sm">Về trang chủ</a>
            </div>
        @endif
    </div>
</section>
@endsection

@push('css')
<style>
    .yk-group + .yk-group { margin-top: var(--space-10); }

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
