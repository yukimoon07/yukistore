@extends('layouts.user.app')

@section('title', 'Star Rating (Prompt #016)')

@section('content')
<div class="container" style="padding-top: 32px; padding-bottom: 64px;">
    <!-- Breadcrumb -->
    <nav class="gaming-breadcrumb" aria-label="Breadcrumb" style="margin-bottom: 24px;">
        <a href="/"><i class="fa-solid fa-house"></i> Trang Chủ</a>
        <span class="gaming-breadcrumb-sep">/</span>
        <span class="gaming-breadcrumb-current">Star Rating (Prompt #016)</span>
    </nav>

    <!-- Header & Guide -->
    <div style="text-align: center; max-width: 680px; margin: 0 auto 36px;">
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px; background: rgba(232, 179, 60, 0.15); border: 1px solid rgba(232, 179, 60, 0.35); color: var(--yuki-gold, #E8B33C); font-weight: 700; font-size: 0.85rem; margin-bottom: 12px;">
            <i class="fa-solid fa-star"></i> Lucian UX/UI Prompt #016 · Toggles & Pickers
        </div>
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 2rem; font-weight: 800; color: #f8fafc; margin: 0 0 10px;">
            Star Rating with Sparkle & Word Animation
        </h1>
        <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6; margin: 0;">
            Di chuột qua các ngôi sao để xem trước trạng thái, nhấn chuột để các ngôi sao bung tỏa tia sáng (sparkle pop) lần lượt từ trái sang phải, kèm số điểm và từ mô tả đổi màu tương ứng. Hỗ trợ đầy đủ các phím mũi tên và số 0–5.
        </p>
    </div>

    <!-- Theme & Controls Bar -->
    <div style="display: flex; justify-content: center; align-items: center; gap: 16px; margin-bottom: 32px;">
        <button type="button" onclick="setRatingTheme('dark')" id="btnDark" style="padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-moon"></i> Dark Theme
        </button>
        <button type="button" onclick="setRatingTheme('light')" id="btnLight" style="padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-sun"></i> Light Theme
        </button>
    </div>

    <!-- Component Presentation Arena -->
    <div id="ratingPreviewContainer" style="display: flex; justify-content: center; align-items: center; padding: 48px 20px; border-radius: 24px; background: rgba(11, 15, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 20px 48px rgba(0,0,0,0.5); min-height: 380px; transition: background 0.3s ease;">
        <div style="background: var(--ux-surface, rgba(255,255,255,0.04)); padding: 24px 32px; border-radius: 24px; border: 1px solid var(--ux-line, rgba(255,255,255,0.1)); box-shadow: 0 16px 40px rgba(0,0,0,0.3);">
            <x-star-rating-u016 
                id="ratingPreviewComponent"
                question="How was your order?"
                :value="0"
                inputName="demo_rating"
            />
        </div>
    </div>

    <!-- Live Event Feedback -->
    <div style="max-width: 480px; margin: 24px auto 0; text-align: center;">
        <div id="liveFeedbackText" style="font-size: 0.9rem; font-weight: 600; color: #94a3b8; min-height: 24px;">
            Chưa có đánh giá nào được chọn
        </div>
    </div>

    <!-- Quick Test Guide -->
    <div style="max-width: 600px; margin: 32px auto 0; padding: 20px 24px; border-radius: 14px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); font-size: 0.88rem; color: #94a3b8; line-height: 1.6;">
        <div style="font-weight: 700; color: #f8fafc; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Checklist kiểm tra tính năng:
        </div>
        <ul style="margin: 0; padding-left: 20px;">
            <li><strong>Hover / Di chuột qua các sao:</strong> Các ngôi sao phóng to nhẹ và xoay -6°, viền và màu bên trong sáng mờ (preview fill 72%).</li>
            <li><strong>Bấm chọn số sao:</strong> Các ngôi sao bung nổ hiệu ứng sparkle (`u016-pop` và `u016-spark`) lần lượt từ trái qua phải cách nhau 70ms.</li>
            <li><strong>Đổi màu và nhãn từ:</strong> Điểm số (0.0 đến 5.0) và từ mô tả (Tap a star / Poor / Fair / Good / Great / Excellent) trượt cuộn theo trục Y với màu tương ứng.</li>
            <li><strong>Phím điều hướng bàn phím:</strong>
                <ul style="margin-top: 4px;">
                    <li>Phím <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">→</kbd> hoặc <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">↑</kbd>: Tăng thêm 1 sao</li>
                    <li>Phím <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">←</kbd> hoặc <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">↓</kbd>: Giảm bớt 1 sao</li>
                    <li>Phím <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">Home</kbd>: Đặt về 0; phím <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">End</kbd>: Đặt lên 5</li>
                    <li>Các phím số <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">0</kbd>–<kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">5</kbd>: Chọn trực tiếp số sao tương ứng</li>
                </ul>
            </li>
            <li><strong>Reduced motion:</strong> Khi bật <code>prefers-reduced-motion: reduce</code>, hiệu ứng sparkle và stagger được tắt tự động, chuyển trạng thái tức thời.</li>
        </ul>
    </div>
</div>

<script>
    function setRatingTheme(theme) {
        var container = document.getElementById('ratingPreviewContainer');
        var comp = document.getElementById('ratingPreviewComponent');
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

    document.addEventListener('u016:rate', function (e) {
        var feedback = document.getElementById('liveFeedbackText');
        if (feedback && e.detail) {
            feedback.innerHTML = '<span style="color:' + e.detail.color + '; font-weight:700;">★ ' + e.detail.value + ' sao — ' + e.detail.word + '</span> (Đã kích hoạt event u016:rate)';
        }
    });
</script>
@endsection
