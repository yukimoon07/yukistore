@extends('layouts.user.app')

@section('title', $title)

@section('content')
    <x-hero-header title="{{ mb_strtoupper($category->name, 'UTF-8') }}" description="" />

    <section class="yk-section" style="padding-top: 24px; padding-bottom: 60px;">
        <div class="container">
            <!-- Breadcrumb Navigation -->
            <nav class="gaming-breadcrumb" aria-label="Breadcrumb" style="margin-bottom: 20px;">
                <a href="/"><i class="fa-solid fa-house"></i> {{ __('shop.nav_home') }}</a>
                <span class="gaming-breadcrumb-sep">/</span>
                <a href="{{ route('random.show-all') }}">{{ __('shop.nav_random_categories') ?? 'Danh Mục Random' }}</a>
                <span class="gaming-breadcrumb-sep">/</span>
                <span class="gaming-breadcrumb-current">{{ $category->name }}</span>
            </nav>

            @if($category->description)
            <div class="yk-card yk-card--desc" style="margin-bottom: 24px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px; padding: 18px 24px; font-size: 0.95rem; line-height: 1.6; color: #cbd5e1;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; color: var(--yuki-gold, #E8B33C); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <i class="fa-solid fa-circle-info"></i> Giới thiệu danh mục
                </div>
                {!! nl2br(e($category->description)) !!}
            </div>
            @endif

            <div class="yk-card yk-buy-hero">
                {{-- Left: Large Prominent Media Frame with Ambient Backdrop Blur & Crisp Contain Image --}}
                <div class="yk-buy__media-frame" onclick="openImageModal('{{ $category->thumbnail ?: asset('img/logo.svg') }}', '{{ addslashes($category->name) }}')">
                    {{-- Ambient blur layer filling the frame seamlessly --}}
                    <div class="yk-buy__media-blur" style="background-image: url('{{ $category->thumbnail ?: asset('img/logo.svg') }}');"></div>
                    
                    {{-- High-definition Crisp Image: object-fit contain ensures 100% of banner & text is accommodated without cropping --}}
                    <img src="{{ $category->thumbnail ?: asset('img/logo.svg') }}" 
                         alt="{{ $category->name }}" 
                         class="yk-buy__media-img"
                         loading="eager">

                    {{-- Badges / Overlays --}}
                    <div class="yk-buy__media-overlay">
                        <span class="yk-buy__badge">
                            <i class="fa-solid fa-dice" style="color: #fbbf24;"></i>
                            <span>{{ str_contains(strtolower($category->slug), 'tui-mu') ? 'TÚI MÙ MAY MẮN' : 'THỬ VẬN MAY' }}</span>
                        </span>
                        
                        <button type="button" class="yk-buy__zoom-btn" title="Phóng to ảnh xem chi tiết" aria-label="Phóng to ảnh">
                            <i class="fa-solid fa-maximize"></i>
                        </button>
                    </div>
                </div>

                {{-- Right: Information & Purchase Column --}}
                <div class="yk-buy__content">
                    <div class="yk-buy__header">
                        <div class="yk-buy__pill">
                            <i class="fa-solid fa-shield-halved"></i> Giao dịch tự động 100%
                        </div>
                        <h1 class="yk-buy__heading">{{ $category->name }}</h1>
                        <p class="yk-buy__lead">
                            Nhận ngay 1 tài khoản ngẫu nhiên từ hệ thống. Thông tin đăng nhập hiển thị ngay trên màn hình và lưu vĩnh viễn trong mục <em>Lịch sử mua tài khoản</em>.
                        </p>
                    </div>

                    {{-- Stats Grid (Còn lại / Giá bán) --}}
                    <div class="yk-buy__stats-grid">
                        <div class="yk-buy__stat-card">
                            <div class="yk-buy__stat-label">
                                <i class="fa-solid fa-boxes-stacked" style="color: #38bdf8;"></i> Trạng thái kho
                            </div>
                            <div class="yk-buy__stat-val {{ $availableCount > 0 ? 'yk-buy__stat-val--avail' : 'yk-buy__stat-val--empty' }}">
                                {{ number_format($availableCount) }} <span style="font-size: 0.85rem; font-weight: 500; color: #94a3b8;">tài khoản</span>
                            </div>
                        </div>

                        <div class="yk-buy__stat-card">
                            <div class="yk-buy__stat-label">
                                <i class="fa-solid fa-tag" style="color: var(--yuki-gold, #E8B33C);"></i> Giá niêm yết
                            </div>
                            <div class="yk-buy__stat-price">
                                {{ number_format($price) }} <span style="font-size: 0.95rem; font-weight: 600;">đ</span>
                            </div>
                        </div>
                    </div>

                    {{-- Trust Guarantees List --}}
                    <div class="yk-buy__guarantees">
                        <div class="yk-buy__guarantee-item">
                            <i class="fa-solid fa-bolt-lightning" style="color: var(--yuki-gold, #E8B33C);"></i>
                            <span>Giao tài khoản tự động 24/7 chỉ trong 3 giây</span>
                        </div>
                        <div class="yk-buy__guarantee-item">
                            <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                            <span>Cam kết chuẩn thông tin & tỉ lệ trúng đúng công bố</span>
                        </div>
                        <div class="yk-buy__guarantee-item">
                            <i class="fa-solid fa-rotate-left" style="color: #60a5fa;"></i>
                            <span>Hỗ trợ bảo hành 1 đổi 1 nếu tài khoản sai mật khẩu</span>
                        </div>
                    </div>

                    {{-- Actions Button --}}
                    <div class="yk-buy__actions">
                        @if($availableCount > 0)
                            <button type="button" class="yk-btn-hero-purchase" onclick="openPurchaseModal()">
                                <i class="fa-solid fa-gift"></i>
                                <span>MUA NGẪU NHIÊN NGAY</span>
                            </button>
                        @else
                            <button type="button" class="yk-btn-hero-purchase disabled" disabled>
                                <i class="fa-solid fa-ban"></i>
                                <span>TẠM HẾT HÀNG TRONG KHO</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Full-Size Image Preview Lightbox Modal -->
    <div id="imageLightboxModal" class="yk-lightbox-modal" onclick="closeImageModal()" style="display: none;">
        <button type="button" class="yk-lightbox-close" onclick="closeImageModal()" aria-label="Đóng">&times;</button>
        <div class="yk-lightbox-content" onclick="event.stopPropagation()">
            <img id="lightboxImg" src="" alt="Full view">
            <div id="lightboxCaption" class="yk-lightbox-caption"></div>
        </div>
    </div>

    <!-- Modal Mua Hàng -->
    <div id="randomPurchaseModal" class="yk-modal" role="dialog" aria-modal="true" aria-labelledby="randomPurchaseTitle">
        <div class="yk-modal__panel">
            <div class="yk-modal__head">
                <h3 class="yk-modal__title" id="randomPurchaseTitle">Xác nhận mua</h3>
                <button type="button" class="yk-modal__close" onclick="closePurchaseModal()" aria-label="Đóng">&times;</button>
            </div>

            <div class="yk-modal__body">
                <div class="yk-picks">
                    <img src="{{ $category->thumbnail ?: asset('img/favicon.svg') }}" alt="" width="88" height="52">
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
        /* Hero Card Layout */
        .yk-buy-hero {
            display: grid;
            grid-template-columns: minmax(360px, 520px) 1fr;
            gap: 36px;
            padding: 32px;
            background: linear-gradient(145deg, #0d1527, #070a12);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.6);
            align-items: start;
        }

        @media (max-width: 991px) {
            .yk-buy-hero {
                grid-template-columns: 1fr;
                gap: 24px;
                padding: 20px;
            }
        }

        /* Large Prominent Media Frame */
        .yk-buy__media-frame {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #060911;
            border: 1.5px solid rgba(232, 179, 60, 0.35);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.6), 0 0 24px rgba(232, 179, 60, 0.12);
            cursor: pointer;
            transition: transform 300ms ease, border-color 300ms ease, box-shadow 300ms ease;
            align-self: flex-start;
        }

        .yk-buy__media-frame:hover {
            border-color: var(--yuki-gold, #E8B33C);
            box-shadow: 0 20px 44px rgba(0, 0, 0, 0.7), 0 0 30px rgba(232, 179, 60, 0.25);
            transform: translateY(-2px);
        }

        /* Ambient Blur Backdrop Layer */
        .yk-buy__media-blur {
            position: absolute;
            inset: -14px;
            background-size: cover;
            background-position: center;
            filter: blur(28px) brightness(0.35) saturate(1.4);
            transform: scale(1.1);
            pointer-events: none;
            z-index: 1;
        }

        /* Main Image: Sharp, Crisp, 100% Contained, Never Cropped */
        .yk-buy__media-img {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
            border-radius: 12px;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            transition: transform 350ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .yk-buy__media-frame:hover .yk-buy__media-img {
            transform: scale(1.02);
        }

        /* Overlays & Controls */
        .yk-buy__media-overlay {
            position: absolute;
            inset: 0;
            z-index: 3;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px;
            pointer-events: none;
        }

        .yk-buy__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            background: rgba(11, 15, 25, 0.85);
            border: 1px solid rgba(232, 179, 60, 0.45);
            color: var(--yuki-gold, #E8B33C);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            backdrop-filter: blur(6px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .yk-buy__zoom-btn {
            pointer-events: auto;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(11, 15, 25, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(6px);
            font-size: 0.9rem;
            transition: all 200ms ease;
        }

        .yk-buy__zoom-btn:hover {
            background: var(--yuki-gold, #E8B33C);
            color: #0b0f19;
            border-color: var(--yuki-gold, #E8B33C);
            transform: scale(1.08);
        }

        /* Right Content Column */
        .yk-buy__content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .yk-buy__pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
            font-size: 0.8rem;
            font-weight: 600;
            width: fit-content;
            margin-bottom: 8px;
        }

        .yk-buy__heading {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.85rem;
            font-weight: 700;
            color: #f8fafc;
            line-height: 1.25;
            margin: 0;
            letter-spacing: -0.01em;
        }

        .yk-buy__lead {
            color: #94a3b8;
            font-size: 0.92rem;
            line-height: 1.6;
            margin: 0;
        }

        /* Stats Grid */
        .yk-buy__stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
        }

        .yk-buy__stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 14px 16px;
        }

        .yk-buy__stat-label {
            font-size: 0.8rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .yk-buy__stat-val {
            font-size: 1.35rem;
            font-weight: 700;
            color: #f8fafc;
            font-family: 'Space Grotesk', sans-serif;
        }

        .yk-buy__stat-val--avail {
            color: #34d399;
        }

        .yk-buy__stat-val--empty {
            color: #f87171;
        }

        .yk-buy__stat-price {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--yuki-gold, #E8B33C);
            line-height: 1.1;
        }

        /* Guarantees List */
        .yk-buy__guarantees {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .yk-buy__guarantee-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.86rem;
            color: #cbd5e1;
        }

        /* Hero Purchase Button */
        .yk-btn-hero-purchase {
            width: 100%;
            padding: 16px 28px;
            border-radius: 12px;
            background: linear-gradient(135deg, #E8B33C 0%, #C2922A 100%);
            border: none;
            color: #0b0f19;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(232, 179, 60, 0.35);
            transition: all 250ms ease;
        }

        .yk-btn-hero-purchase:hover:not(:disabled) {
            transform: translateY(-2px);
            filter: brightness(1.08);
            box-shadow: 0 12px 30px rgba(232, 179, 60, 0.5);
        }

        .yk-btn-hero-purchase:active:not(:disabled) {
            transform: translateY(0);
        }

        .yk-btn-hero-purchase.disabled,
        .yk-btn-hero-purchase:disabled {
            background: rgba(255, 255, 255, 0.08);
            color: #64748b;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: none;
            cursor: not-allowed;
        }

        /* Modal picks */
        .yk-picks {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-3);
            margin-bottom: var(--space-4);
            border: 1px solid var(--yuki-line);
            border-radius: var(--radius-card);
            background: var(--yuki-ink-850);
        }
        .yk-picks img {
            width: 88px;
            height: 52px;
            border-radius: 8px;
            object-fit: contain;
            background: #060911;
            border: 1px solid rgba(232, 179, 60, 0.3);
            flex-shrink: 0;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
        .yk-picks__name {
            margin: 0 0 4px;
            font-size: var(--text-base);
            font-weight: 600;
            color: #f8fafc;
        }
        .yk-picks__meta {
            font: var(--font-caption);
            color: var(--yuki-text-muted);
        }
        .yk-picks__price {
            color: var(--yuki-gold);
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        /* Qty and fields */
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

        /* Lightbox Full-Size Image Preview Modal */
        .yk-lightbox-modal {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(0, 0, 0, 0.88);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            animation: ykFadeIn 200ms ease;
        }
        @keyframes ykFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .yk-lightbox-close {
            position: absolute;
            top: 20px;
            right: 24px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            font-size: 28px;
            line-height: 1;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 150ms ease;
            z-index: 2;
        }
        .yk-lightbox-close:hover {
            background: rgba(239, 68, 68, 0.8);
            border-color: #ef4444;
        }
        .yk-lightbox-content {
            max-width: 90vw;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .yk-lightbox-content img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8), 0 0 0 1px rgba(255,255,255,0.15);
        }
        .yk-lightbox-caption {
            margin-top: 14px;
            color: #f8fafc;
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
        }
    </style>
@endsection

@push('scripts')
<script>
    const unitPrice = {{ $price }};
    const maxQty = {{ $availableCount }};
    
    // Lightbox image functions
    function openImageModal(imgSrc, title) {
        var modal = document.getElementById('imageLightboxModal');
        var img = document.getElementById('lightboxImg');
        var caption = document.getElementById('lightboxCaption');
        if (modal && img) {
            img.src = imgSrc;
            if (caption) caption.innerText = title || '';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImageModal() {
        var modal = document.getElementById('imageLightboxModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Purchase Modal functions
    function openPurchaseModal() {
        @auth
            document.getElementById('randomPurchaseModal').style.display = 'flex';
            // Initialize discount handler
            initDiscountHandler('random_account', {{ $category->id }}, unitPrice);
        @else
            if (typeof FuiToast !== 'undefined') {
                FuiToast.error("Vui lòng đăng nhập để mua hàng!");
            } else {
                alert("Vui lòng đăng nhập để mua hàng!");
            }
            setTimeout(() => {
                window.location.href = "{{ route('login') }}";
            }, 1200);
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
            if (typeof FuiToast !== 'undefined') {
                FuiToast.error("Số lượng không hợp lệ!");
            } else {
                alert("Số lượng không hợp lệ!");
            }
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
                if (typeof FuiToast !== 'undefined') {
                    FuiToast.success(data.message);
                } else {
                    alert(data.message);
                }
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1500);
            } else {
                if (typeof FuiToast !== 'undefined') {
                    FuiToast.error(data.message);
                } else {
                    alert(data.message);
                }
                btn.disabled = false;
                btn.innerText = 'Xác nhận mua';
            }
        })
        .catch(err => {
            console.error(err);
            if (typeof FuiToast !== 'undefined') {
                FuiToast.error("Có lỗi xảy ra, vui lòng thử lại sau.");
            } else {
                alert("Có lỗi xảy ra, vui lòng thử lại sau.");
            }
            btn.disabled = false;
            btn.innerText = 'Xác nhận mua';
        });
    }

    // Escape listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
            closePurchaseModal();
        }
    });
</script>
@endpush
