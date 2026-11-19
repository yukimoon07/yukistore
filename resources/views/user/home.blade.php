@extends('layouts.user.app')
@section('title', 'Trang chủ - ' . config_get('site_name', 'Yuki Store'))
@section('content')

<h1 class="sr-only">{{ config_get('site_name', 'Yuki Store') }} — Tài khoản game, dịch vụ cày thuê và nạp tiền tự động</h1>

@php
    // Banner Admin cấu hình; nếu chưa có thì dùng ảnh danh mục đã đóng gói kèm shop.
    $siteBannerRaw = config_get('site_banner');
    $banners = json_decode($siteBannerRaw, true);
    if (!is_array($banners) && !empty($siteBannerRaw)) {
        $banners = [$siteBannerRaw];
    }
    if (empty($banners)) {
        $banners = [
            asset('storage/banners/hero-lien-quan.png'),
            asset('storage/banners/hero-fc-mobile.png'),
            asset('storage/banners/hero-genshin.png'),
        ];
    }
@endphp

{{-- ============================================================
     HERO — banner là chủ thể, panel nạp tiền là cột phụ
     ============================================================ --}}
<section class="yk-hero">
    <div class="container">
        <div class="yk-hero__grid">

            {{-- Cột trái: banner xoay vòng --}}
            <div class="yk-hero__stage">
                <div class="yk-slider" id="bannerSlider">
                    <div class="yk-slider__track" id="sliderTrack">
                        @foreach($banners as $index => $banner)
                            <div class="yk-slide {{ $index === 0 ? 'is-active' : '' }}"
                                 data-index="{{ $index }}"
                                 aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                                <img src="{{ $banner }}"
                                     alt="{{ config_get('site_name', 'Yuki Store') }} — banner {{ $index + 1 }}"
                                     class="yk-slide__img"
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                     width="1200" height="675"
                                     onerror="this.closest('.yk-slide').classList.add('is-broken')">
                                <div class="yk-slide__fallback" aria-hidden="true">
                                    <span class="yk-slide__fallback-title">{{ config_get('site_name', 'Yuki Store') }}</span>
                                    <span class="yk-slide__fallback-text">Tài khoản game, dịch vụ cày thuê và nạp tiền tự động</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(count($banners) > 1)
                        <button class="yk-slider__arrow yk-slider__arrow--prev" id="sliderPrev" aria-label="Banner trước">
                            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                        </button>
                        <button class="yk-slider__arrow yk-slider__arrow--next" id="sliderNext" aria-label="Banner sau">
                            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                        </button>
                        <div class="yk-slider__dots" role="tablist" aria-label="Chọn banner">
                            @foreach($banners as $index => $banner)
                                <button class="yk-slider__dot {{ $index === 0 ? 'is-active' : '' }}"
                                        data-index="{{ $index }}"
                                        role="tab"
                                        aria-label="Banner {{ $index + 1 }}"
                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"></button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="yk-hero__actions">
                    <a href="{{ route('category.show-all') }}" class="yk-btn yk-btn--primary yk-btn--lg">
                        <i class="fa-solid fa-grip" aria-hidden="true"></i> Xem tất cả danh mục
                    </a>
                    <a href="{{ route('service.show-all') }}" class="yk-btn yk-btn--ghost yk-btn--lg">
                        <i class="fa-solid fa-bolt" aria-hidden="true"></i> Dịch vụ cày thuê
                    </a>
                </div>
            </div>

            {{-- Cột phải: nạp tiền + top tháng --}}
            <aside class="yk-hero__panel" aria-label="Nạp tiền và bảng xếp hạng">
                <div class="yk-panel">
                    <div class="yk-panel__tabs" role="tablist">
                        <button class="yk-panel__tab is-active" role="tab" aria-selected="true"
                                data-tab="tab-top" onclick="switchTab(this, 'tab-top')">
                            <i class="fa-solid fa-trophy" aria-hidden="true"></i>
                            {{ __('shop.hero_top_deposit', ['month' => date('m')]) }}
                        </button>
                        <button class="yk-panel__tab" role="tab" aria-selected="false"
                                data-tab="tab-reward" onclick="switchTab(this, 'tab-reward')">
                            <i class="fa-solid fa-gift" aria-hidden="true"></i>
                            {{ __('shop.hero_rewards') }}
                        </button>
                    </div>

                    <div class="yk-panel__body tab-content is-active" id="tab-top">
                        @forelse($topDepositors as $depositor)
                            @php
                                $uname = $depositor->user->username ?? 'User';
                                $len = mb_strlen($uname);
                                $maskedName = $len > 4
                                    ? mb_substr($uname, 0, 2) . '***' . mb_substr($uname, -2)
                                    : mb_substr($uname, 0, 2) . '***';
                            @endphp
                            <div class="yk-rank {{ $loop->iteration <= 3 ? 'yk-rank--top' : '' }}">
                                <span class="yk-rank__no">{{ $loop->iteration }}</span>
                                <span class="yk-rank__user">{{ $maskedName }}</span>
                                <span class="yk-rank__amount">{{ number_format($depositor->total_amount) }} đ</span>
                            </div>
                        @empty
                            <p class="yk-panel__empty">{{ __('shop.hero_empty_deposit') }}</p>
                        @endforelse
                    </div>

                    <div class="yk-panel__body tab-content" id="tab-reward" hidden>
                        <div class="yk-panel__rich">
                            {!! \App\Support\HtmlSanitizer::clean(config_get('top_deposit_reward')) ?: '<p class="yk-panel__empty">' . __('shop.hero_reward_updating') . '</p>' !!}
                        </div>
                    </div>

                    <div class="yk-panel__foot">
                        <button type="button" class="yk-btn yk-btn--primary yk-btn--block"
                                onclick="document.getElementById('depositMethodModal').style.display='flex'">
                            <i class="fa-solid fa-wallet" aria-hidden="true"></i>
                            {{ __('shop.hero_deposit_now') }}
                        </button>
                    </div>
                </div>

                <ul class="yk-trust" aria-label="Cam kết dịch vụ">
                    <li><i class="fa-solid fa-bolt" aria-hidden="true"></i> Giao dịch tự động 24/7</li>
                    <li><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Bảo mật thông tin tài khoản</li>
                    <li><i class="fa-solid fa-headset" aria-hidden="true"></i> Hỗ trợ khi đơn gặp sự cố</li>
                </ul>
            </aside>

        </div>
    </div>
</section>

{{-- ============================================================
     THÔNG BÁO — marquee
     ============================================================ --}}
@php
    $marqueeNotifications = \App\Models\Notification::orderBy('id', 'desc')->get();
@endphp
@if($marqueeNotifications->count() > 0)
<div class="container">
    <div class="yk-marquee" aria-label="Thông báo mới">
        <div class="yk-marquee__badge">
            <i class="fa-solid fa-satellite-dish" aria-hidden="true"></i>
            <span class="yk-marquee__label">{{ __('shop.marquee_title') }}</span>
        </div>
        <div class="yk-marquee__viewport">
            <div class="yk-marquee__track">
                @foreach([0, 1] as $dup)
                    <div class="yk-marquee__group" @if($dup) aria-hidden="true" @endif>
                        @foreach($marqueeNotifications as $notif)
                            <span class="yk-marquee__item">
                                <i class="{{ str_starts_with($notif->class_favicon, 'fa-') ? 'fa-solid ' . $notif->class_favicon : $notif->class_favicon }}" aria-hidden="true"></i>
                                {{ $notif->content }}
                            </span>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- ============================================================
     GIAO DỊCH GẦN ĐÂY
     ============================================================ --}}
@if($recentTransactions->count() > 0)
<div class="container">
    <div class="yk-ticker" aria-label="Giao dịch gần đây">
        <div class="yk-ticker__track">
            @foreach([0, 1] as $dup)
                <div class="yk-ticker__group" @if($dup) aria-hidden="true" @endif>
                    @foreach($recentTransactions as $transaction)
                        <span class="yk-ticker__item">
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            <strong>{{ mb_substr($transaction->user->username ?? __('shop.ticker_guest'), 0, 3) }}***</strong>
                            @if ($transaction->type == 'deposit')
                                {{ __('shop.ticker_deposited') }}
                            @elseif($transaction->type == 'withdraw')
                                {{ __('shop.ticker_withdrew') }}
                            @elseif($transaction->type == 'purchase')
                                {{ __('shop.ticker_purchased') }}
                            @elseif($transaction->type == 'refund')
                                {{ __('shop.ticker_refunded') }}
                            @endif
                            <span class="yk-ticker__price">{{ number_format($transaction->amount) }}đ</span>
                            <span class="yk-ticker__time">{{ $transaction->created_at->diffForHumans() }}</span>
                        </span>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ============================================================
     FLASH SALE
     ============================================================ --}}
@if((isset($timelineCampaigns) && $timelineCampaigns->count() > 0) || (isset($activeFlashSale) && $activeFlashSale))
<section class="yk-section yk-section--tight">
    <div class="container">
        <div class="yk-flash">
            <div class="yk-flash__head">
                <div class="yk-flash__title">
                    <i class="fa-solid fa-bolt" aria-hidden="true"></i>
                    {{ isset($activeFlashSale) && $activeFlashSale ? mb_strtoupper($activeFlashSale->campaign_name) : 'FLASH SALE' }}
                </div>
                <div class="yk-flash__timer">
                    <span class="yk-flash__timer-label">{{ $activeFlashSale ? __('shop.fs_ends_in') : __('shop.fs_starts_in') }}</span>
                    <div class="yk-countdown" aria-live="off">
                        <span class="yk-countdown__box"><b id="fs-days">00</b><small>NGÀY</small></span>
                        <span class="yk-countdown__box"><b id="fs-hours">00</b><small>GIỜ</small></span>
                        <span class="yk-countdown__box"><b id="fs-mins">00</b><small>PHÚT</small></span>
                        <span class="yk-countdown__box"><b id="fs-secs">00</b><small>GIÂY</small></span>
                    </div>
                </div>
            </div>

            <div class="yk-flash__timeline">
                @php
                    $slots = ['1:00', '6:00', '8:00', '12:00', '15:00', '18:00', '20:00', '22:00'];
                    $currentHour = (int) date('H');
                    $activeIndex = 0;
                    foreach ($slots as $index => $time) {
                        if ($currentHour >= (int) explode(':', $time)[0]) {
                            $activeIndex = $index;
                        }
                    }
                @endphp
                @foreach($slots as $index => $time)
                    <div class="yk-flash__slot {{ $index < $activeIndex ? 'is-past' : ($index == $activeIndex ? 'is-active' : '') }}">
                        <span class="yk-flash__slot-time">{{ $time }}</span>
                        <span class="yk-flash__slot-status">{{ $index < $activeIndex ? 'Đã diễn ra' : ($index == $activeIndex ? 'Đang diễn ra' : 'Sắp diễn ra') }}</span>
                    </div>
                @endforeach
            </div>

            @if(isset($flashSales) && count($flashSales) > 0)
                <div class="yk-grid yk-flash__grid">
                    @foreach($flashSales as $fs)
                        <a href="{{ $fs->is_random ? route('random.index', ['slug' => $fs->slug]) : route('category.index', ['slug' => $fs->slug]) }}" class="yk-tile">
                            <div class="yk-tile__media">
                                <img src="{{ $fs->thumbnail }}" alt="{{ $fs->name }}" loading="lazy" width="400" height="225" onerror="this.style.visibility='hidden'">
                                @if($fs->flash_sale_old_price > 0 && $fs->flash_sale_new_price > 0 && $fs->flash_sale_old_price > $fs->flash_sale_new_price)
                                    @php $discountPercent = round((($fs->flash_sale_old_price - $fs->flash_sale_new_price) / $fs->flash_sale_old_price) * 100); @endphp
                                    <span class="yk-tile__badge yk-tile__badge--sale">-{{ $discountPercent }}%</span>
                                @endif
                            </div>
                            <div class="yk-tile__body">
                                <h3 class="yk-tile__title">{{ $fs->name }}</h3>
                                <div class="yk-tile__meta">
                                    @if($fs->flash_sale_old_price)
                                        <span class="yk-price-old">{{ number_format($fs->flash_sale_old_price) }}đ</span>
                                    @endif
                                    @if($fs->flash_sale_new_price)
                                        <span class="yk-tile__price" style="position:static;">{{ number_format($fs->flash_sale_new_price) }}đ</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="yk-flash__empty">
                    <i class="fa-regular fa-clock" aria-hidden="true"></i>
                    <p>Chưa đến khung giờ Flash Sale. Sản phẩm sẽ hiển thị khi khung giờ bắt đầu.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endif

@php
    $groupedCategories = [];
    if (isset($gameGroups)) {
        foreach ($gameGroups as $gg) {
            $groupedCategories[trim($gg->name)] = collect([]);
        }
    }
    foreach ($categories as $category) {
        if ($category->gameGroup) {
            $groupName = trim($category->gameGroup->name);
        } else {
            $groupName = $category->platform ? trim($category->platform) : 'Danh mục khác';
        }
        if (!isset($groupedCategories[$groupName])) {
            $groupedCategories[$groupName] = collect([]);
        }
        $groupedCategories[$groupName]->push($category);
    }
    $firstGroup = true;
@endphp

{{-- ============================================================
     DANH MỤC THEO GAME
     ============================================================ --}}
@foreach ($groupedCategories as $platform => $group)
    @php $activeCategoryCount = $group->where('active', 1)->count(); @endphp
    @if($activeCategoryCount > 0)
        <section class="yk-section yk-section--tight" @if($firstGroup) id="categories" @endif>
            <div class="container">
                <div class="yk-section-head">
                    <h2 class="yk-section-title">{{ mb_strtoupper($platform, 'UTF-8') }}</h2>
                    <a href="{{ route('category.group', ['slug' => \Illuminate\Support\Str::slug($platform)]) }}" class="yk-section-link">
                        {{ __('shop.btn_view_all') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @php
                    $sortedGroup = $group->sortBy(function ($item) {
                        $isRandom = ($item instanceof \App\Models\RandomCategory);
                        return ($isRandom ? 10000000 : 0) + ($item->price ?? 0);
                    });
                @endphp

                <div class="yk-grid">
                    @foreach ($sortedGroup as $category)
                        @if($category->active)
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
                                        <span class="yk-tile__badge">{{ __('shop.card_vip_badge') }}</span>
                                    @endif
                                </div>
                                <div class="yk-tile__body">
                                    <h3 class="yk-tile__title">{{ $category->name }}</h3>
                                    <div class="yk-tile__meta">
                                        <span>{{ __('shop.card_stock') }} {{ number_format($category->allAccount) }}</span>
                                        <span class="dot" aria-hidden="true">·</span>
                                        <span>{{ __('shop.card_sold') }} {{ number_format($category->soldCount) }}</span>
                                    </div>
                                    <div class="yk-tile__cta">
                                        <span>{{ __('shop.btn_buy_now') }}</span>
                                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
        @php $firstGroup = false; @endphp
    @endif
@endforeach

{{-- ============================================================
     VÒNG QUAY MAY MẮN
     ============================================================ --}}
@if(isset($LuckWheel) && $LuckWheel->where('active', 1)->count() > 0)
<section class="yk-section yk-section--tight">
    <div class="container">
        <div class="yk-section-head">
            <h2 class="yk-section-title">{{ __('shop.lucky_wheel_title') }}</h2>
            <a href="{{ route('lucky.show-all') }}" class="yk-section-link">
                {{ __('shop.btn_view_all') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="yk-grid">
            @foreach ($LuckWheel as $wheel)
                @if ($wheel->active)
                    <a href="{{ route('lucky.index', ['slug' => $wheel->slug]) }}" class="yk-tile">
                        <div class="yk-tile__media">
                            @if($wheel->thumbnail)
                                <img src="{{ $wheel->thumbnail }}" alt="{{ $wheel->name }}" loading="lazy" width="400" height="225">
                            @else
                                <div class="yk-tile__placeholder" aria-hidden="true"><i class="fa-solid fa-dice"></i></div>
                            @endif
                            <span class="yk-tile__price">{{ number_format($wheel->price_per_spin) }} đ</span>
                        </div>
                        <div class="yk-tile__body">
                            <h3 class="yk-tile__title">{{ $wheel->name }}</h3>
                            <div class="yk-tile__meta">
                                <span>{{ number_format($wheel->soldCount ?? 0) }} {{ __('shop.card_spins') }}</span>
                            </div>
                            <div class="yk-tile__cta">
                                <span>{{ __('shop.spin_now') }}</span>
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     DỊCH VỤ CÀY THUÊ
     ============================================================ --}}
@if($services->where('active', 1)->count() > 0)
<section class="yk-section yk-section--tight" id="game-services">
    <div class="container">
        <div class="yk-section-head">
            <h2 class="yk-section-title">{{ __('shop.service_page_title') }}</h2>
            <a href="{{ route('service.show-all') }}" class="yk-section-link">
                {{ __('shop.btn_view_all') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="yk-grid">
            @foreach ($services as $service)
                @if ($service->active)
                    @php $minPrice = $service->activePackages->min('price'); @endphp
                    <a href="{{ route('service.show', ['slug' => $service->slug]) }}" class="yk-tile">
                        <div class="yk-tile__media">
                            @if ($service->thumbnail)
                                <img src="{{ $service->thumbnail }}" alt="{{ $service->name }}" loading="lazy" width="400" height="225">
                            @else
                                <div class="yk-tile__placeholder" aria-hidden="true"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                            @endif
                            @if ($minPrice)
                                <span class="yk-tile__price">từ {{ number_format($minPrice) }} đ</span>
                            @else
                                <span class="yk-tile__badge">{{ __('shop.service_safe_2fa') }}</span>
                            @endif
                        </div>
                        <div class="yk-tile__body">
                            <h3 class="yk-tile__title">{{ $service->name }}</h3>
                            <div class="yk-tile__meta">
                                <span>{{ number_format($service->orderCount) }} {{ __('shop.card_orders') }}</span>
                            </div>
                            <div class="yk-tile__cta">
                                <span>{{ $minPrice ? __('shop.btn_order_now') : 'Giá đang cập nhật' }}</span>
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     XÁC THỰC MUA HÀNG — chỉ dữ liệu thật, không bịa đánh giá
     ============================================================ --}}
@if (!empty($recentPurchases) && count($recentPurchases) > 0)
<section class="yk-section yk-section--tight">
    <div class="container">
        <div class="yk-section-head">
            <h2 class="yk-section-title">{{ __('shop.reviews_title') }}</h2>
            <a href="{{ route('reviews') }}" class="yk-section-link">
                {{ __('shop.reviews_view_all') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="yk-proof">
            @foreach($recentPurchases as $review)
                <div class="yk-proof__item">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    <div>
                        <div class="yk-proof__name">{{ $review['name'] }}</div>
                        <div class="yk-proof__meta">
                            đã mua nick #{{ $review['id'] }}
                            @if (!empty($review['date'])) · {{ $review['date'] }} @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     THÔNG BÁO CHÀO MỪNG
     ============================================================ --}}
@if (config_get('welcome_modal', false))
<div class="yk-modal" id="announceOverlay" role="dialog" aria-modal="true" aria-labelledby="announceTitle"
     onclick="if(event.target===this)closeAnnounce(false)">
    <div class="yk-modal__panel">
        <div class="yk-modal__head">
            <h2 class="yk-modal__title" id="announceTitle">
                <i class="fa-solid fa-bullhorn" aria-hidden="true"></i> Thông báo
            </h2>
            <button class="yk-modal__close" onclick="closeAnnounce(false)" aria-label="Đóng thông báo">&times;</button>
        </div>
        <div class="yk-modal__body">
            {!! \App\Support\HtmlSanitizer::clean(config_get('home_notification')) !!}
        </div>
        <div class="yk-modal__foot">
            <button class="yk-btn yk-btn--ghost" onclick="closeAnnounce(true)">Đóng trong 2 giờ</button>
            <button class="yk-btn yk-btn--primary" onclick="closeAnnounce(false)">Đóng</button>
        </div>
    </div>
</div>
@endif

@endsection

@push('css')
<style>
/* ---------- HERO ---------- */
.yk-hero { padding: var(--space-6) 0 var(--space-5); }

.yk-hero__grid {
    display: grid;
    gap: var(--space-4);
    grid-template-columns: 1fr;
}
@media (min-width: 1024px) {
    .yk-hero__grid { grid-template-columns: minmax(0, 1fr) 320px; gap: var(--space-5); }
}

.yk-hero__stage { min-width: 0; display: flex; flex-direction: column; gap: var(--space-4); }

/* ---------- SLIDER ---------- */
.yk-slider {
    position: relative;
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid var(--yuki-line);
    background: var(--yuki-ink-900);
    aspect-ratio: 16 / 7;
    min-height: 220px;
}
@media (max-width: 640px) {
    .yk-slider { aspect-ratio: 16 / 10; }
}

.yk-slider__track { position: absolute; inset: 0; }

.yk-slide {
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 420ms ease;
    pointer-events: none;
}
.yk-slide.is-active { opacity: 1; pointer-events: auto; }

.yk-slide__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.yk-slide__fallback {
    display: none;
    position: absolute;
    inset: 0;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    padding: var(--space-6);
    text-align: center;
    background: var(--yuki-ink-850);
}
.yk-slide.is-broken .yk-slide__img { display: none; }
.yk-slide.is-broken .yk-slide__fallback { display: flex; }

.yk-slide__fallback-title {
    font-family: var(--font-display);
    font-size: var(--text-xl);
    font-weight: 700;
}
.yk-slide__fallback-text { font: var(--font-caption); color: var(--yuki-text-muted); }

.yk-slider__arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border-radius: var(--radius-pill);
    border: 1px solid rgba(255, 255, 255, 0.16);
    background: rgba(8, 9, 11, 0.6);
    color: #fff;
    cursor: pointer;
    backdrop-filter: blur(6px);
    transition: background-color var(--transition-fast), border-color var(--transition-fast);
    font-size: 0.8rem;
}
.yk-slider__arrow:hover { background: rgba(8, 9, 11, 0.85); border-color: var(--yuki-gold); }
.yk-slider__arrow--prev { left: var(--space-3); }
.yk-slider__arrow--next { right: var(--space-3); }

.yk-slider__dots {
    position: absolute;
    left: 50%;
    bottom: var(--space-3);
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
}
.yk-slider__dot {
    width: 22px;
    height: 4px;
    padding: 0;
    min-height: 0;
    border: none;
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.35);
    cursor: pointer;
    transition: background-color var(--transition-fast), width var(--transition-fast);
}
.yk-slider__dot.is-active { background: var(--yuki-gold); width: 34px; }

.yk-hero__actions { display: flex; flex-wrap: wrap; gap: var(--space-3); }
.yk-hero__actions .yk-btn { flex: 1 1 180px; }

/* ---------- PANEL ---------- */
.yk-hero__panel { display: flex; flex-direction: column; gap: var(--space-3); min-width: 0; }

.yk-panel {
    display: flex;
    flex-direction: column;
    background: var(--yuki-ink-900);
    border: 1px solid var(--yuki-line);
    border-radius: var(--radius-card);
    overflow: hidden;
    height: 100%;
}

.yk-panel__tabs {
    display: flex;
    border-bottom: 1px solid var(--yuki-line);
}

.yk-panel__tab {
    flex: 1;
    min-height: 42px;
    padding: 0 var(--space-2);
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--yuki-text-muted);
    font: var(--font-caption);
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: color var(--transition-fast), border-color var(--transition-fast);
}
.yk-panel__tab:hover { color: var(--yuki-text); }
.yk-panel__tab.is-active { color: var(--yuki-gold); border-bottom-color: var(--yuki-gold); }

.yk-panel__body { padding: var(--space-2) 0; flex: 1; min-height: 0; }
.yk-panel__body.tab-content { display: none; }
.yk-panel__body.tab-content.is-active { display: block; }

.yk-panel__rich { padding: var(--space-3) var(--space-4); font: var(--font-caption); color: var(--yuki-text-soft); }
.yk-panel__rich img { max-width: 100%; height: auto; border-radius: var(--radius-sm); }

.yk-panel__empty {
    padding: var(--space-6) var(--space-4);
    text-align: center;
    font: var(--font-caption);
    color: var(--yuki-text-muted);
    margin: 0;
}

.yk-panel__foot { padding: var(--space-3); border-top: 1px solid var(--yuki-line); }

/* ---------- RANK ---------- */
.yk-rank {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-2) var(--space-4);
    font: var(--font-caption);
}
.yk-rank + .yk-rank { border-top: 1px solid rgba(255, 255, 255, 0.04); }

.yk-rank__no {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    border-radius: var(--radius-sm);
    background: var(--yuki-ink-800);
    color: var(--yuki-text-muted);
    font-size: var(--text-xs);
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}
.yk-rank--top .yk-rank__no { background: rgba(232, 179, 60, 0.14); color: var(--yuki-gold); }

.yk-rank__user {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: var(--yuki-text-soft);
}
.yk-rank__amount {
    font-family: var(--font-display);
    font-variant-numeric: tabular-nums;
    font-weight: 600;
    color: var(--yuki-text);
}

/* ---------- TRUST ---------- */
.yk-trust {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
}
.yk-trust li {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    font: var(--font-caption);
    color: var(--yuki-text-muted);
}
.yk-trust i { color: var(--yuki-success); width: 14px; text-align: center; flex-shrink: 0; }

/* ---------- MARQUEE ---------- */
.yk-marquee {
    display: flex;
    align-items: stretch;
    overflow: hidden;
    margin-top: var(--space-4);
    border: 1px solid var(--yuki-line);
    border-radius: var(--radius-card);
    background: var(--yuki-ink-900);
}

.yk-marquee__badge {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    padding: 0 var(--space-4);
    border-right: 1px solid var(--yuki-line);
    background: rgba(232, 179, 60, 0.06);
    color: var(--yuki-gold);
    font: var(--font-caption);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
    flex-shrink: 0;
}
.yk-marquee__label { display: none; }
@media (min-width: 640px) { .yk-marquee__label { display: inline; } }

.yk-marquee__viewport { position: relative; flex: 1; min-width: 0; overflow: hidden; }

.yk-marquee__track {
    display: flex;
    width: max-content;
    animation: ykMarquee 48s linear infinite;
}
.yk-marquee:hover .yk-marquee__track { animation-play-state: paused; }

.yk-marquee__group { display: flex; align-items: center; }
.yk-marquee__item {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    padding: 12px var(--space-5) 12px 0;
    font: var(--font-caption);
    color: var(--yuki-text-soft);
    white-space: nowrap;
}
.yk-marquee__item i { color: var(--yuki-gold); font-size: 0.7rem; }

@keyframes ykMarquee {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
}

/* ---------- TICKER ---------- */
.yk-ticker {
    overflow: hidden;
    margin-top: var(--space-3);
    border: 1px solid var(--yuki-line);
    border-radius: var(--radius-card);
    background: var(--yuki-ink-950);
}
.yk-ticker__track { display: flex; width: max-content; animation: ykMarquee 60s linear infinite; }
.yk-ticker:hover .yk-ticker__track { animation-play-state: paused; }
.yk-ticker__group { display: flex; align-items: center; }
.yk-ticker__item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px var(--space-5) 10px 0;
    font: var(--font-caption);
    color: var(--yuki-text-muted);
    white-space: nowrap;
}
.yk-ticker__item i { color: var(--yuki-success); font-size: 0.7rem; }
.yk-ticker__item strong { color: var(--yuki-text-soft); font-weight: 600; }
.yk-ticker__price {
    font-family: var(--font-display);
    font-variant-numeric: tabular-nums;
    font-weight: 600;
    color: var(--yuki-gold);
}
.yk-ticker__time { color: var(--yuki-text-faint); }

/* ---------- SECTION ---------- */
.yk-section--tight { padding: var(--space-8) 0 0; }

/* ---------- FLASH SALE ---------- */
.yk-flash {
    border: 1px solid var(--yuki-line);
    border-radius: var(--radius-card);
    background: var(--yuki-ink-900);
    overflow: hidden;
}

.yk-flash__head {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: var(--space-3);
    padding: var(--space-3) var(--space-4);
    border-bottom: 1px solid var(--yuki-line);
    background: linear-gradient(90deg, rgba(232, 179, 60, 0.1), transparent 60%);
}

.yk-flash__title {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    font-family: var(--font-display);
    font-size: var(--text-md);
    font-weight: 700;
    letter-spacing: 0.02em;
    color: var(--yuki-gold);
}

.yk-flash__timer { display: flex; align-items: center; gap: var(--space-3); }
.yk-flash__timer-label { font: var(--font-caption); color: var(--yuki-text-muted); }

.yk-countdown { display: flex; gap: 4px; }
.yk-countdown__box {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 40px;
    padding: 3px 6px;
    border-radius: var(--radius-sm);
    background: var(--yuki-ink-800);
    border: 1px solid var(--yuki-line);
}
.yk-countdown__box b {
    font-family: var(--font-display);
    font-variant-numeric: tabular-nums;
    font-size: var(--text-base);
    font-weight: 700;
    color: var(--yuki-text);
    line-height: 1.1;
}
.yk-countdown__box small {
    font-size: 9px;
    letter-spacing: 0.06em;
    color: var(--yuki-text-faint);
}

.yk-flash__timeline {
    display: flex;
    overflow-x: auto;
    border-bottom: 1px solid var(--yuki-line);
    scrollbar-width: thin;
}
.yk-flash__slot {
    flex: 1 0 auto;
    min-width: 104px;
    padding: var(--space-2) var(--space-3);
    text-align: center;
    border-right: 1px solid var(--yuki-line);
}
.yk-flash__slot:last-child { border-right: none; }
.yk-flash__slot-time {
    display: block;
    font-family: var(--font-display);
    font-variant-numeric: tabular-nums;
    font-size: var(--text-base);
    font-weight: 600;
    color: var(--yuki-text-faint);
}
.yk-flash__slot-status { display: block; font: var(--font-caption); color: var(--yuki-text-faint); }
.yk-flash__slot.is-active .yk-flash__slot-time,
.yk-flash__slot.is-active .yk-flash__slot-status { color: var(--yuki-gold); }

.yk-flash__grid { padding: var(--space-4); }

.yk-flash__empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-10) var(--space-4);
    text-align: center;
    color: var(--yuki-text-muted);
    font: var(--font-caption);
}
.yk-flash__empty i { font-size: 1.5rem; color: var(--yuki-text-faint); }
.yk-flash__empty p { margin: 0; }

/* ---------- PROOF ---------- */
.yk-proof {
    display: grid;
    gap: var(--space-3);
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
}
.yk-proof__item {
    display: flex;
    align-items: flex-start;
    gap: var(--space-3);
    padding: var(--space-3) var(--space-4);
    border: 1px solid var(--yuki-line);
    border-radius: var(--radius-card);
    background: var(--yuki-ink-900);
}
.yk-proof__item i { color: var(--yuki-success); margin-top: 2px; flex-shrink: 0; }
.yk-proof__name {
    font-family: var(--font-display);
    font-size: var(--text-base);
    font-weight: 600;
}
.yk-proof__meta { font: var(--font-caption); color: var(--yuki-text-muted); }

/* ---------- PLACEHOLDER ---------- */
.yk-tile__placeholder {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    color: var(--yuki-text-faint);
    font-size: 1.75rem;
}

.yk-tile__badge--sale {
    left: auto;
    right: var(--space-2);
    bottom: var(--space-2);
    top: auto;
    background: var(--yuki-danger);
    border-color: transparent;
    color: #fff;
    font-weight: 700;
}
</style>
@endpush

@push('scripts')
<script>
    /* Tabs bảng xếp hạng */
    function switchTab(btn, tabId) {
        document.querySelectorAll('.yk-panel__tab').forEach(function (b) {
            b.classList.remove('is-active');
            b.setAttribute('aria-selected', 'false');
        });
        document.querySelectorAll('.yk-panel .tab-content').forEach(function (t) {
            t.classList.remove('is-active');
            t.hidden = true;
        });
        btn.classList.add('is-active');
        btn.setAttribute('aria-selected', 'true');
        var target = document.getElementById(tabId);
        if (target) { target.classList.add('is-active'); target.hidden = false; }
    }

    /* Banner slider */
    (function () {
        var slider = document.getElementById('bannerSlider');
        if (!slider) return;

        var slides = slider.querySelectorAll('.yk-slide');
        var dots = slider.querySelectorAll('.yk-slider__dot');
        var prev = document.getElementById('sliderPrev');
        var next = document.getElementById('sliderNext');
        var index = 0;
        var timer = null;

        function show(i) {
            if (!slides.length) return;
            index = (i + slides.length) % slides.length;
            slides.forEach(function (s, n) {
                var on = n === index;
                s.classList.toggle('is-active', on);
                s.setAttribute('aria-hidden', on ? 'false' : 'true');
            });
            dots.forEach(function (d, n) {
                var on = n === index;
                d.classList.toggle('is-active', on);
                d.setAttribute('aria-selected', on ? 'true' : 'false');
            });
        }

        function start() {
            if (slides.length < 2) return;
            stop();
            timer = setInterval(function () { show(index + 1); }, 6000);
        }
        function stop() { if (timer) { clearInterval(timer); timer = null; } }

        dots.forEach(function (d) {
            d.addEventListener('click', function () {
                show(parseInt(d.dataset.index, 10));
                start();
            });
        });
        if (prev) prev.addEventListener('click', function () { show(index - 1); start(); });
        if (next) next.addEventListener('click', function () { show(index + 1); start(); });

        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);

        /* Tôn trọng prefers-reduced-motion: không tự chạy */
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) start();
    })();
</script>

@if (config_get('welcome_modal', false))
<script>
    (function () {
        var KEY = 'announce_dismiss_until';
        var overlay = document.getElementById('announceOverlay');
        if (!overlay) return;

        var until = localStorage.getItem(KEY);
        if (until && Date.now() < parseInt(until, 10)) {
            overlay.style.display = 'none';
        } else {
            overlay.style.display = 'flex';
        }
    })();

    function closeAnnounce(silent2h) {
        var el = document.getElementById('announceOverlay');
        if (el) el.style.display = 'none';
        if (silent2h) {
            localStorage.setItem('announce_dismiss_until', Date.now() + 2 * 60 * 60 * 1000);
        }
    }

    /* Flash Sale countdown */
    (function () {
        @php
            $targetEndTime = null;
            if (isset($activeFlashSale)) {
                $targetEndTime = $activeFlashSale->end_time;
            } elseif (isset($timelineCampaigns) && count($timelineCampaigns) > 0) {
                $upcoming = $timelineCampaigns->filter(function ($c) {
                    return \Carbon\Carbon::parse($c->start_time)->isFuture();
                })->first();
                if ($upcoming) { $targetEndTime = $upcoming->start_time; }
            }
        @endphp

        @if($targetEndTime)
            var endTime = new Date("{{ \Carbon\Carbon::parse($targetEndTime)->format('Y/m/d H:i:s') }}");
        @else
            var now = new Date();
            var endTime = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 0);
        @endif

        function pad(n) { return String(n).padStart(2, '0'); }

        function updateTimer() {
            var elDays = document.getElementById('fs-days');
            if (!elDays) return;

            var diff = Math.max(0, endTime - new Date());
            elDays.innerText = pad(Math.floor(diff / 86400000));
            document.getElementById('fs-hours').innerText = pad(Math.floor((diff / 3600000) % 24));
            document.getElementById('fs-mins').innerText = pad(Math.floor((diff / 60000) % 60));
            document.getElementById('fs-secs').innerText = pad(Math.floor((diff / 1000) % 60));
        }

        updateTimer();
        setInterval(updateTimer, 1000);
    })();
</script>
@endif
@endpush
