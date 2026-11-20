@extends('layouts.user.app')

@section('title', $category->name)

@section('content')
    <x-hero-header title="{{ $category->name }}" description="{{ $category->description }}" />

    <section class="yk-section">
        <div class="container">

            {{-- Bộ lọc --}}
            <form method="GET" action="{{ url()->current() }}" class="yk-filter" aria-label="Bộ lọc tài khoản">
                <div class="yk-filter__field">
                    <label for="filter-code">{{ __('shop.filter_code') }}</label>
                    <input type="number" id="filter-code" name="code" placeholder="VD: 123"
                           value="{{ request('code') }}" inputmode="numeric">
                </div>

                <div class="yk-filter__field">
                    <label for="filter-price-from">{{ __('shop.filter_price_from') }}</label>
                    <input type="number" id="filter-price-from" name="price_from" min="0" step="1000"
                           placeholder="0" value="{{ request('price_from') }}" inputmode="numeric">
                </div>

                <div class="yk-filter__field">
                    <label for="filter-price-to">{{ __('shop.filter_price_to') }}</label>
                    <input type="number" id="filter-price-to" name="price_to" min="0" step="1000"
                           placeholder="Vô hạn" value="{{ request('price_to') }}" inputmode="numeric">
                </div>

                @foreach($dynamicKeys as $key)
                    <div class="yk-filter__field">
                        <label for="filter-detail-{{ $loop->iteration }}">{{ $key }}</label>
                        <input type="text" id="filter-detail-{{ $loop->iteration }}"
                               name="details[{{ $key }}]" placeholder="{{ $key }}..."
                               value="{{ request("details.{$key}") }}">
                    </div>
                @endforeach

                <div class="yk-filter__field">
                    <label for="filter-status">{{ __('shop.filter_status_all') }}</label>
                    <select id="filter-status" name="status">
                        <option value="">{{ __('shop.filter_status_all') }}</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('shop.filter_status_available') }}</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>{{ __('shop.filter_status_sold') }}</option>
                    </select>
                </div>

                <div class="yk-filter__field">
                    <label for="filter-sort">Sắp xếp</label>
                    <select id="filter-sort" name="sort">
                        <option value="" {{ !request('sort') ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                    </select>
                </div>

                <div class="yk-filter__actions">
                    <button type="submit" class="yk-btn yk-btn--primary yk-btn--sm">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> {{ __('shop.filter_search_btn') }}
                    </button>
                    <a href="{{ url()->current() }}" class="yk-btn yk-btn--ghost yk-btn--sm">
                        <i class="fa-solid fa-rotate-right" aria-hidden="true"></i> {{ __('shop.filter_reset_btn') }}
                    </a>
                </div>
            </form>

            {{-- Dismissible Chips cho các bộ lọc đang chọn --}}
            @php
                $hasActiveFilters = request()->hasAny(['code', 'price_from', 'price_to', 'status', 'sort']);
            @endphp
            @if($hasActiveFilters)
                <div class="yk-active-chips" style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin: 16px 0 24px 0;">
                    <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">Bộ lọc đang áp dụng:</span>
                    @if(request('code'))
                        <span class="yk-chip">
                            Mã: #{{ request('code') }}
                            <a href="{{ request()->fullUrlWithQuery(['code' => null]) }}" class="yk-chip__remove" aria-label="Xóa bộ lọc mã tài khoản">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            </a>
                        </span>
                    @endif
                    @if(request('price_from'))
                        <span class="yk-chip">
                            Giá từ: {{ number_format(request('price_from')) }} đ
                            <a href="{{ request()->fullUrlWithQuery(['price_from' => null]) }}" class="yk-chip__remove" aria-label="Xóa bộ lọc giá từ">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            </a>
                        </span>
                    @endif
                    @if(request('price_to'))
                        <span class="yk-chip">
                            Giá đến: {{ number_format(request('price_to')) }} đ
                            <a href="{{ request()->fullUrlWithQuery(['price_to' => null]) }}" class="yk-chip__remove" aria-label="Xóa bộ lọc giá đến">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            </a>
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="yk-chip">
                            Trạng thái: {{ request('status') === 'available' ? 'Còn hàng' : 'Đã bán' }}
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="yk-chip__remove" aria-label="Xóa bộ lọc trạng thái">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            </a>
                        </span>
                    @endif
                    @if(request('sort'))
                        <span class="yk-chip">
                            Sắp xếp: {{ request('sort') === 'price_asc' ? 'Giá thấp → cao' : (request('sort') === 'price_desc' ? 'Giá cao → thấp' : 'Cũ nhất') }}
                            <a href="{{ request()->fullUrlWithQuery(['sort' => null]) }}" class="yk-chip__remove" aria-label="Xóa bộ lọc sắp xếp">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            </a>
                        </span>
                    @endif
                    <a href="{{ url()->current() }}" style="font-size: 0.78rem; color: #f87171; text-decoration: none; margin-left: 4px; font-weight: 600;">
                        Xóa tất cả
                    </a>
                </div>
            @endif

            {{-- Danh sách tài khoản --}}
            <div class="yk-grid yk-grid--account">
                @forelse($accounts as $account)
                    <article class="yk-tile yk-account" style="position: relative;">
                        <div class="yk-tile__media">
                            <a href="{{ route('account.show', ['id' => $account->id]) }}" aria-label="Xem chi tiết tài khoản #{{ $account->id }}">
                                <img src="{{ $account->thumb }}" alt="Ảnh xem trước tài khoản #{{ $account->id }}"
                                     loading="lazy" width="400" height="250"
                                     onerror="this.style.visibility='hidden'">
                            </a>
                            <span class="yk-account__code">#{{ $account->id }}</span>
                            
                            {{-- Fully Rounded Pill cho trạng thái --}}
                            <span class="yk-pill {{ $account->status === 'available' ? 'yk-pill--success' : 'yk-pill--muted' }}" style="position: absolute; top: var(--space-2); right: var(--space-2); z-index: 2;">
                                <i class="fa-solid {{ $account->status === 'available' ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="font-size: 10px;"></i>
                                {{ $account->status === 'available' ? 'Còn hàng' : 'Đã bán' }}
                            </span>

                            <span class="yk-tile__price">{{ number_format($account->price) }} đ</span>
                        </div>

                        <div class="yk-tile__body">
                            {{-- Category Tag & Meatball Button --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                <span class="yk-tag">
                                    <i class="fa-solid fa-tag" style="font-size: 10px; color: var(--yuki-gold, #E8B33C);"></i>
                                    {{ $category->name }}
                                </span>

                                {{-- Horizontal-dots Meatball Button (button[aria-haspopup="menu"]) --}}
                                <div style="position: relative;">
                                    <button type="button" 
                                            class="yk-meatball-btn" 
                                            aria-haspopup="menu" 
                                            aria-expanded="false" 
                                            aria-label="Tùy chọn khác cho tài khoản #{{ $account->id }}"
                                            onclick="toggleCardMeatball(this, event)">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                    <div class="yk-meatball-menu" role="menu" style="display: none; position: absolute; right: 0; top: 100%; min-width: 150px; background: #0d1322; border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; box-shadow: 0 12px 28px rgba(0,0,0,0.7); z-index: 100; padding: 4px;">
                                        <a href="{{ route('account.show', ['id' => $account->id]) }}" role="menuitem" class="account-menu-item" style="padding: 6px 10px; font-size: 0.8rem;">
                                            <span class="iconify" data-icon="ant-design:eye-outlined"></span> Xem chi tiết
                                        </a>
                                        <button type="button" role="menuitem" class="account-menu-item" style="padding: 6px 10px; font-size: 0.8rem;" onclick="navigator.clipboard.writeText('{{ route('account.show', ['id' => $account->id]) }}'); alert('Đã sao chép link tài khoản!');">
                                            <span class="iconify" data-icon="ant-design:copy-outlined"></span> Sao chép link
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @php
                                $details = is_array($account->details) ? $account->details : json_decode($account->details, true) ?? [];
                            @endphp

                            @if(count($details) > 0)
                                <dl class="yk-account__specs">
                                    @foreach(array_slice($details, 0, 4) as $detail)
                                        <div class="yk-account__spec">
                                            <dt>{{ $detail['key'] ?? '' }}</dt>
                                            <dd>{{ \Illuminate\Support\Str::limit($detail['value'] ?? '', 24) }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            @endif

                            <div class="yk-tile__cta">
                                <span class="yk-account__cardprice">
                                    {{ __('shop.detail_price_card') }}: {{ number_format($account->price / ((100 - config_get('payment.card.discount_percent')) / 100)) }} đ
                                </span>
                                <a href="{{ route('account.show', ['id' => $account->id]) }}" class="yk-btn yk-btn--primary yk-btn--sm">
                                    {{ __('shop.btn_view_detail') }}
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="yk-empty">
                        <div class="yk-empty__icon"><i class="fa-solid fa-box-open" aria-hidden="true"></i></div>
                        <h2 class="yk-empty__title">{{ __('shop.empty_accounts') }}</h2>
                        <p class="yk-empty__text">Danh mục này chưa có tài khoản hoặc không có tài khoản nào khớp bộ lọc.</p>
                        <a href="{{ url()->current() }}" class="yk-btn yk-btn--ghost yk-btn--sm">Xóa bộ lọc</a>
                    </div>
                @endforelse
            </div>

            {{-- Phân trang --}}
            <div class="yk-pagination">
                {{ $accounts->links('user.pagination.custom') }}
            </div>
        </div>
    </section>
@endsection

@push('css')
<style>
    /* ---- Filter bar ---- */
    .yk-filter {
        display: grid;
        gap: var(--space-3);
        grid-template-columns: 1fr;
        padding: var(--space-4);
        margin-bottom: var(--space-6);
        background: var(--yuki-ink-900);
        border: 1px solid var(--yuki-line);
        border-radius: var(--radius-card);
    }
    @media (min-width: 640px) {
        .yk-filter { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .yk-filter { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .yk-filter__field:nth-last-child(-n+2) { grid-column: span 2; }
    }

    .yk-filter__actions {
        display: flex;
        align-items: flex-end;
        gap: var(--space-2);
    }

    /* ---- Account card ---- */
    .yk-grid--account { grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); }

    /* Thẻ tài khoản là <article>, không phải <a> — CTA nằm bên trong */
    .yk-account { cursor: default; }
    .yk-account:hover { transform: none; }
    .yk-account .yk-tile__media { aspect-ratio: 16 / 10; }

    .yk-account__code {
        position: absolute;
        left: var(--space-2);
        bottom: var(--space-2);
        padding: 2px var(--space-2);
        border-radius: var(--radius-sm);
        background: rgba(8, 9, 11, 0.82);
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-family: var(--font-display);
        font-variant-numeric: tabular-nums;
        font-size: var(--text-xs);
        font-weight: 600;
        color: var(--yuki-text);
    }

    .yk-account__status {
        position: absolute;
        top: var(--space-2);
        left: var(--space-2);
        padding: 2px var(--space-2);
        border-radius: var(--radius-sm);
        font: var(--font-caption);
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .yk-account__status--in  { background: rgba(63, 191, 127, 0.9); color: #06281a; }
    .yk-account__status--out { background: rgba(8, 9, 11, 0.85); color: var(--yuki-text-faint); border: 1px solid rgba(255,255,255,0.12); }

    .yk-account__specs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-1) var(--space-2);
        margin: 0 0 var(--space-3);
    }
    .yk-account__spec {
        display: flex;
        justify-content: space-between;
        gap: var(--space-2);
        padding: 3px 6px;
        border-radius: var(--radius-sm);
        background: var(--yuki-ink-850);
        border: 1px solid rgba(255, 255, 255, 0.04);
        overflow: hidden;
    }
    .yk-account__spec dt {
        color: var(--yuki-text-muted);
        font-size: var(--text-xs);
        white-space: nowrap;
    }
    .yk-account__spec dd {
        margin: 0;
        color: var(--yuki-text);
        font-size: var(--text-xs);
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .yk-account__cardprice {
        font: var(--font-caption);
        color: var(--yuki-text-muted);
        font-variant-numeric: tabular-nums;
    }

    /* Tài khoản đã bán: mờ ảnh + ẩn CTA giá thẻ */
    .yk-account--sold .yk-tile__media img { filter: grayscale(0.7) brightness(0.7); }

    /* ---- Pagination ---- */
    .yk-pagination {
        display: flex;
        justify-content: center;
        margin-top: var(--space-8);
    }
</style>
@endpush

@push('js')
<script>
    function toggleCardMeatball(btn, e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        var menu = btn.nextElementSibling;
        if (!menu) return;
        var isOpen = menu.style.display === 'block';
        document.querySelectorAll('.yk-meatball-menu').forEach(function(m) {
            m.style.display = 'none';
        });
        document.querySelectorAll('.yk-meatball-btn').forEach(function(b) {
            b.setAttribute('aria-expanded', 'false');
        });
        if (!isOpen) {
            menu.style.display = 'block';
            btn.setAttribute('aria-expanded', 'true');
        }
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.yk-meatball-btn') && !e.target.closest('.yk-meatball-menu')) {
            document.querySelectorAll('.yk-meatball-menu').forEach(function(m) {
                m.style.display = 'none';
            });
            document.querySelectorAll('.yk-meatball-btn').forEach(function(b) {
                b.setAttribute('aria-expanded', 'false');
            });
        }
    });
</script>
@endpush
