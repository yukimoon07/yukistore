@extends('layouts.user.app')

@section('title', $title)

@section('content')
    <x-hero-header title="{{ mb_strtoupper($category->name, 'UTF-8') }}" description="" />

    <section class="yk-section">
        <div class="container">
            @if($category->description)
            <div class="yk-card yk-card--desc">
                {!! nl2br(e($category->description)) !!}
            </div>
            @endif

            <div class="yk-card yk-buy">
                <div class="yk-buy__media">
                    <img src="{{ $category->thumbnail ?: asset('img/logo.svg') }}" alt="{{ $category->name }}" width="520" height="293">
                </div>
                <div class="yk-buy__info">
                    <h2 class="yk-buy__title">Mua tài khoản ngẫu nhiên</h2>
                    <p class="yk-buy__text">
                        Bạn nhận được 1 tài khoản ngẫu nhiên từ danh mục <strong>{{ $category->name }}</strong>.
                        Giao acc tự động ngay sau thanh toán.
                    </p>

                    <div class="yk-buy__stats">
                        <div class="yk-buy__stat">
                            <span class="yk-buy__stat-label">Còn lại</span>
                            <span class="yk-buy__stat-value yk-buy__stat-value--ok">{{ number_format($availableCount) }} acc</span>
                        </div>
                        <div class="yk-buy__stat">
                            <span class="yk-buy__stat-label">Giá</span>
                            <span class="yk-price-now">{{ number_format($price) }} đ</span>
                        </div>
                    </div>

                    <div class="yk-buy__actions">
                        @if($availableCount > 0)
                            <button type="button" class="yk-btn yk-btn--primary yk-btn--lg" onclick="openPurchaseModal()">
                                <i class="fa-solid fa-gift" aria-hidden="true"></i> Mua ngẫu nhiên
                            </button>
                        @else
                            <button type="button" class="yk-btn yk-btn--ghost yk-btn--lg" disabled>Đã hết hàng</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Mua Hàng -->
    <div id="randomPurchaseModal" class="yk-modal" role="dialog" aria-modal="true" aria-labelledby="randomPurchaseTitle">
        <div class="yk-modal__panel">
            <div class="yk-modal__head">
                <h3 class="yk-modal__title" id="randomPurchaseTitle">Xác nhận mua</h3>
                <button type="button" class="yk-modal__close" onclick="closePurchaseModal()" aria-label="Đóng">&times;</button>
            </div>

            <div class="yk-modal__body">
                <div class="yk-picks">
                    <img src="{{ $category->thumbnail ?: asset('img/favicon.svg') }}" alt="" width="60" height="60">
                    <div>
                        <h4 class="yk-picks__name">{{ $category->name }}</h4>
                        <div class="yk-picks__meta">
                            Giá: <span class="yk-picks__price">{{ number_format($price) }}đ</span>/acc
                            · Còn {{ number_format($availableCount) }} tài khoản
                        </div>
                    </div>
                </div>

                <div class="yk-qty">
                    <label class="yk-qty__label" for="purchaseQuantity">Số lượng mua</label>
                    <div class="yk-qty__controls">
                        <button type="button" class="yk-qty__btn" onclick="changeQuantity(-1)" aria-label="Giảm số lượng">−</button>
                        <input type="number" id="purchaseQuantity" value="1" min="1" max="{{ $availableCount }}"
                               onchange="updateTotalPrice()" inputmode="numeric">
                        <button type="button" class="yk-qty__btn" onclick="changeQuantity(1)" aria-label="Tăng số lượng">+</button>
                    </div>
                </div>

                <div class="yk-total">
                    <span>Tổng thanh toán</span>
                    <span id="totalPriceDisplay" class="yk-total__value">{{ number_format($price) }}đ</span>
                </div>

                <div class="yk-field">
                    <label for="discountCode">Mã giảm giá</label>
                    <div class="yk-field__row">
                        <input type="text" id="discountCode" placeholder="Nhập mã giảm giá...">
                        <button type="button" class="yk-btn yk-btn--ghost" onclick="applyDiscount()">Áp dụng</button>
                    </div>
                    <div id="discount-message" class="modal__discount-message"></div>
                </div>
            </div>

            <div class="yk-modal__foot">
                <button type="button" class="yk-btn yk-btn--ghost" onclick="closePurchaseModal()">Hủy</button>
                <button type="button" class="yk-btn yk-btn--primary" id="btnSubmitPurchase" onclick="submitPurchase()">Xác nhận mua</button>
            </div>
        </div>
    </div>

    <style>
        .yk-card--desc { margin-bottom: var(--space-4); color: var(--yuki-text-soft); }
        .yk-card--desc :last-child { margin-bottom: 0; }

        .yk-buy { display: flex; flex-wrap: wrap; gap: var(--space-5); padding: var(--space-5); }
        .yk-buy__media { flex: 0 0 auto; width: 300px; max-width: 100%; }
        .yk-buy__media img { width: 100%; border-radius: var(--radius-card); border: 1px solid var(--yuki-line); object-fit: cover; aspect-ratio: 16/9; }
        .yk-buy__info { flex: 1 1 280px; min-width: 0; display: flex; flex-direction: column; }
        .yk-buy__title { font: var(--font-h3); margin: 0 0 var(--space-2); }
        .yk-buy__text { color: var(--yuki-text-muted); font: var(--font-caption); }
        .yk-buy__stats { display: flex; gap: var(--space-8); margin: var(--space-4) 0; flex-wrap: wrap; }
        .yk-buy__stat { display: flex; flex-direction: column; gap: 2px; }
        .yk-buy__stat-label { font: var(--font-caption); color: var(--yuki-text-muted); }
        .yk-buy__stat-value--ok { color: var(--yuki-success); font-weight: 600; }
        .yk-buy__actions { margin-top: auto; }

        .yk-picks { display: flex; gap: var(--space-3); padding: var(--space-3); margin-bottom: var(--space-4); border: 1px solid var(--yuki-line); border-radius: var(--radius-card); background: var(--yuki-ink-850); }
        .yk-picks img { width: 60px; height: 60px; border-radius: var(--radius-sm); object-fit: cover; flex-shrink: 0; }
        .yk-picks__name { margin: 0 0 2px; font-size: var(--text-base); font-weight: 600; }
        .yk-picks__meta { font: var(--font-caption); color: var(--yuki-text-muted); }
        .yk-picks__price { color: var(--yuki-gold); font-weight: 600; font-variant-numeric: tabular-nums; }

        .yk-qty { margin-bottom: var(--space-4); }
        .yk-qty__label { display: block; font: var(--font-caption); font-weight: 600; color: var(--yuki-text-soft); margin-bottom: var(--space-2); }
        .yk-qty__controls { display: flex; gap: var(--space-2); }
        .yk-qty__btn { width: 44px; height: 44px; border-radius: var(--radius-input); border: 1px solid var(--yuki-line-strong); background: var(--yuki-ink-850); color: var(--yuki-text); font-size: 1.1rem; cursor: pointer; }
        .yk-qty__btn:hover { border-color: var(--yuki-gold); color: var(--yuki-gold); }
        .yk-qty__controls input { width: 80px; height: 44px; text-align: center; font-weight: 600; font-variant-numeric: tabular-nums; }

        .yk-total { display: flex; justify-content: space-between; align-items: center; padding: var(--space-3) 0; margin-bottom: var(--space-4); border-top: 1px solid var(--yuki-line); border-bottom: 1px solid var(--yuki-line); }
        .yk-total span:first-child { color: var(--yuki-text-muted); font: var(--font-caption); }
        .yk-total__value { font-family: var(--font-display); font-variant-numeric: tabular-nums; font-size: var(--text-lg); font-weight: 700; color: var(--yuki-gold); }

        .yk-field label { display: block; font: var(--font-caption); color: var(--yuki-text-muted); margin-bottom: var(--space-1); }
        .yk-field__row { display: flex; gap: var(--space-2); }
        .yk-field__row input { flex: 1; }

        #randomPurchaseModal { display: none; }
        #randomPurchaseModal[style*="flex"] { display: flex; align-items: center; justify-content: center; }
    </style>
@endsection

@push('scripts')
<script>
    const unitPrice = {{ $price }};
    const maxQty = {{ $availableCount }};
    
    function openPurchaseModal() {
        @auth
            document.getElementById('randomPurchaseModal').style.display = 'flex';
            // Initialize discount handler
            initDiscountHandler('random_account', {{ $category->id }}, unitPrice);
        @else
            FuiToast.error("Vui lòng đăng nhập để mua hàng!");
            setTimeout(() => {
                window.location.href = "{{ route('login') }}";
            }, 1500);
        @endauth
    }

    function closePurchaseModal() {
        document.getElementById('randomPurchaseModal').style.display = 'none';
        document.getElementById('purchaseQuantity').value = 1;
        document.getElementById('discountCode').value = '';
        updateTotalPrice();
    }

    function changeQuantity(delta) {
        const input = document.getElementById('purchaseQuantity');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > maxQty) val = maxQty;
        input.value = val;
        updateTotalPrice();
    }

    function updateTotalPrice() {
        let val = parseInt(document.getElementById('purchaseQuantity').value);
        if (isNaN(val) || val < 1) val = 1;
        if (val > maxQty) val = maxQty;
        document.getElementById('purchaseQuantity').value = val;
        
        const total = val * unitPrice;
        document.getElementById('totalPriceDisplay').innerText = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        
        // Update originalPrice in discount handler
        if (typeof discountHandler !== 'undefined') {
            discountHandler.originalPrice = total;
            discountHandler.discountedPrice = total;
        }
    }
    
    function applyDiscount() {
        const qty = parseInt(document.getElementById('purchaseQuantity').value);
        checkDiscountCode('random_account', qty);
    }

    function submitPurchase() {
        const btn = document.getElementById('btnSubmitPurchase');
        const qty = parseInt(document.getElementById('purchaseQuantity').value);
        const discountCode = document.getElementById('discountCode').value.trim();

        if (qty < 1 || qty > maxQty) {
            FuiToast.error("Số lượng không hợp lệ!");
            return;
        }
        
        let finalDiscountCode = discountCode;
        if (typeof discountHandler !== 'undefined' && discountHandler.discountCode) {
            finalDiscountCode = discountHandler.discountCode;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

        fetch("{{ route('random.category.purchase', $category->slug) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                quantity: qty,
                discount_code: finalDiscountCode
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                FuiToast.success(data.message);
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1500);
            } else {
                FuiToast.error(data.message);
                btn.disabled = false;
                btn.innerText = 'Xác nhận mua';
            }
        })
        .catch(err => {
            console.error(err);
            FuiToast.error("Có lỗi xảy ra, vui lòng thử lại sau.");
            btn.disabled = false;
            btn.innerText = 'Xác nhận mua';
        });
    }
</script>
@endpush
