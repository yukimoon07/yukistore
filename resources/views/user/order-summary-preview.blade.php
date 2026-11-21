@extends('layouts.user.app')

@section('title', 'Order Summary & Promo Code (Prompt #230)')

@section('content')
<div class="container" style="padding-top: 32px; padding-bottom: 64px;">
    <!-- Breadcrumb -->
    <nav class="gaming-breadcrumb" aria-label="Breadcrumb" style="margin-bottom: 24px;">
        <a href="/"><i class="fa-solid fa-house"></i> Trang Chủ</a>
        <span class="gaming-breadcrumb-sep">/</span>
        <span class="gaming-breadcrumb-current">Order Summary with Promo Code (Prompt #230)</span>
    </nav>

    <!-- Header & Guide -->
    <div style="text-align: center; max-width: 680px; margin: 0 auto 36px;">
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px; background: rgba(232, 179, 60, 0.15); border: 1px solid rgba(232, 179, 60, 0.35); color: var(--yuki-gold, #E8B33C); font-weight: 700; font-size: 0.85rem; margin-bottom: 12px;">
            <i class="fa-solid fa-tag"></i> Lucian UX/UI Prompt #230 · Commerce & Pricing
        </div>
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 2rem; font-weight: 800; color: #f8fafc; margin: 0 0 10px;">
            Order Summary Recalculates with Promo Code
        </h1>
        <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6; margin: 0;">
            Nhấn vào <strong>“Have a promo code?”</strong> và nhập mã <code style="background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 6px; color: var(--yuki-gold, #E8B33C); font-weight: 700;">WELCOME10</code> để xem hiệu ứng gấp thành pill, dòng giảm giá 10% trượt vào và tổng tiền đếm ngược mượt mà.
        </p>
    </div>

    <!-- Theme & Controls Bar -->
    <div style="display: flex; justify-content: center; align-items: center; gap: 16px; margin-bottom: 32px;">
        <button type="button" onclick="setPreviewTheme('dark')" id="btnDark" style="padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-moon"></i> Dark Theme
        </button>
        <button type="button" onclick="setPreviewTheme('light')" id="btnLight" style="padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-sun"></i> Light Theme
        </button>
    </div>

    <!-- Component Presentation Arena -->
    <div id="previewContainer" style="display: flex; justify-content: center; align-items: center; padding: 40px 20px; border-radius: 24px; background: rgba(11, 15, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 20px 48px rgba(0,0,0,0.5); min-height: 540px; transition: background 0.3s ease;">
        <x-order-summary-u230 
            id="orderSummaryDemo"
            title="Order summary"
            :items="[
                ['Linen shirt', 'White · M', 39, 'linear-gradient(140deg,#E0E7FF,#A5B4FC)'],
                ['Cotton tote', 'Natural', 18, 'linear-gradient(140deg,#FEF3C7,#FCD34D)'],
            ]"
            shipping="3"
            shippingLabel="Shipping"
            currency="$"
            promoCode="WELCOME10"
            discountPercent="10"
            buttonText="Pay now"
        />
    </div>

    <!-- Quick Test Guide -->
    <div style="max-width: 600px; margin: 40px auto 0; padding: 20px 24px; border-radius: 14px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); font-size: 0.88rem; color: #94a3b8; line-height: 1.6;">
        <div style="font-weight: 700; color: #f8fafc; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Checklist kiểm tra:
        </div>
        <ul style="margin: 0; padding-left: 20px;">
            <li>Bấm <strong>“Have a promo code?”</strong>: trường nhập mã trượt mở mượt mà bên dưới.</li>
            <li>Nhập mã sai (VD: <code>ABC</code>) và nhấn Apply: trường lắc rung và xuất hiện thông báo đỏ <em>This code isn’t valid</em>.</li>
            <li>Nhập đúng mã <code>WELCOME10</code>: trường gấp thành tag xanh <em>WELCOME10 · 10% off</em>, dòng Discount (-$5.70) trượt vào, tổng tiền đếm ngược từ $60.00 xuống $54.30, giá cũ gạch ngang và nhãn <em>$5.70 saved</em> bung ra.</li>
            <li>Bấm dấu <strong>×</strong> trên pill: mọi trạng thái phục hồi về ban đầu.</li>
            <li>Hỗ trợ bàn phím: bấm phím <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">Enter</kbd> trong ô nhập để kích hoạt Apply.</li>
            <li>Hỗ trợ <code>prefers-reduced-motion</code>: tắt animation tự động khi người dùng bật chế độ giảm chuyển động.</li>
        </ul>
    </div>
</div>

<script>
    function setPreviewTheme(theme) {
        var container = document.getElementById('previewContainer');
        var comp = document.getElementById('orderSummaryDemo');
        if (theme === 'light') {
            container.style.background = '#e5e7eb';
            if (comp) comp.setAttribute('data-theme', 'light');
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            container.style.background = 'rgba(11, 15, 25, 0.6)';
            if (comp) comp.setAttribute('data-theme', 'dark');
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    }
</script>
@endsection
