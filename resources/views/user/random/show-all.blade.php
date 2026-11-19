@extends('layouts.user.app')

@section('title', 'Danh mục random')

@section('content')
    <x-hero-header title="Danh mục random"
                   description="Mua tài khoản ngẫu nhiên theo từng gói giá. Hệ thống giao acc tự động sau khi thanh toán." />

    <section class="yk-section">
        <div class="container">
            <div class="yk-grid">
                @forelse ($categories as $category)
                    <a href="{{ route('random.index', ['slug' => $category->slug]) }}" class="yk-tile">
                        <div class="yk-tile__media">
                            @if($category->thumbnail)
                                <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" loading="lazy" width="400" height="225">
                            @else
                                <div class="yk-tile__placeholder" aria-hidden="true">
                                    <i class="fa-solid fa-gift"></i>
                                </div>
                            @endif

                            @if(isset($category->price) && $category->price > 0)
                                <span class="yk-tile__price">{{ number_format($category->price) }} đ</span>
                            @endif
                        </div>

                        <div class="yk-tile__body">
                            <h2 class="yk-tile__title">{{ $category->name }}</h2>
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
                @empty
                    <div class="yk-empty">
                        <div class="yk-empty__icon"><i class="fa-solid fa-gift" aria-hidden="true"></i></div>
                        <h2 class="yk-empty__title">Chưa có danh mục random nào</h2>
                        <p class="yk-empty__text">Hệ thống đang cập nhật, vui lòng quay lại sau.</p>
                        <a href="{{ route('category.show-all') }}" class="yk-btn yk-btn--ghost yk-btn--sm">Xem danh mục khác</a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@push('css')
<style>
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
