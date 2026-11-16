<!DOCTYPE html>
<html lang="vi" data-theme="dark">
@include('layouts.user.head')
<body style="min-height:100vh;display:flex;flex-direction:column;margin:0;">
    <div id="fui-toast"></div>
    <!-- Navbar -->
    @include('layouts.user.header')
    
    <main style="padding-top:64px;flex:1;">
        @yield('content')
    </main>

    @include('layouts.user.footer')

    <!-- Deposit Method Modal -->
    <div id="depositMethodModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:10000;align-items:flex-end;justify-content:center;backdrop-filter:blur(4px);" onclick="if(event.target===this)this.style.display='none'">
        <div class="deposit-modal-content" style="border-radius:16px 16px 0 0;padding:24px 20px 32px;width:100%;max-width:480px;animation:slideUpModal .25s ease-out;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h3 class="deposit-modal-title" style="font-size:1.1rem;font-weight:700;margin:0;">Chọn phương thức nạp</h3>
                <button class="deposit-modal-close" onclick="document.getElementById('depositMethodModal').style.display='none'" style="background:none;border:none;font-size:1.3rem;cursor:pointer;padding:4px;">✕</button>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <a href="{{ route('profile.deposit-card') }}" class="deposit-modal-link" style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:12px;border:1px solid #e5e7eb;text-decoration:none;transition:all .2s;">
                    <div class="deposit-modal-icon-bg card-icon-bg" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="iconify card-icon" data-icon="ant-design:credit-card-outlined" style="font-size:1.4rem;"></span>
                    </div>
                    <div>
                        <div class="deposit-modal-text" style="font-weight:600;font-size:0.95rem;">Nạp thẻ cào</div>
                        <div class="deposit-modal-subtext" style="font-size:0.78rem;margin-top:2px;">Viettel, Vinaphone, Mobifone...</div>
                    </div>
                    <span class="iconify deposit-modal-arrow" style="margin-left:auto;font-size:1.2rem;" data-icon="ant-design:right-outlined"></span>
                </a>
                <a href="{{ route('profile.deposit-atm') }}" class="deposit-modal-link" style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:12px;border:1px solid #e5e7eb;text-decoration:none;transition:all .2s;">
                    <div class="deposit-modal-icon-bg atm-icon-bg" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="iconify atm-icon" data-icon="ant-design:bank-outlined" style="font-size:1.4rem;"></span>
                    </div>
                    <div>
                        <div class="deposit-modal-text" style="font-weight:600;font-size:0.95rem;">Nạp ngân hàng</div>
                        <div class="deposit-modal-subtext" style="font-size:0.78rem;margin-top:2px;">Chuyển khoản tự động, nhận tiền ngay</div>
                    </div>
                    <span class="iconify deposit-modal-arrow" style="margin-left:auto;font-size:1.2rem;" data-icon="ant-design:right-outlined"></span>
                </a>
                @if (config_get('payment.usdt.active', true))
                <a href="{{ route('profile.deposit-usdt') }}" class="deposit-modal-link" style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:12px;border:1px solid #e5e7eb;text-decoration:none;transition:all .2s;">
                    <div class="deposit-modal-icon-bg usdt-icon-bg" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-brands fa-usps usdt-icon" style="font-size:1.4rem;"></i>
                    </div>
                    <div>
                        <div class="deposit-modal-text" style="font-weight:600;font-size:0.95rem;">Nạp USDT</div>
                        <div class="deposit-modal-subtext" style="font-size:0.78rem;margin-top:2px;">Thanh toán bằng tiền điện tử</div>
                    </div>
                    <span class="iconify deposit-modal-arrow" style="margin-left:auto;font-size:1.2rem;" data-icon="ant-design:right-outlined"></span>
                </a>
                @endif
            </div>
        </div>
    </div>
    <style>
        @keyframes slideUpModal { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .deposit-modal-content { background: #ffffff; }
        .deposit-modal-title { color: #111827; }
        .deposit-modal-close { color: #9ca3af; }
        .deposit-modal-link { color: inherit; border-color: #e5e7eb; }
        .deposit-modal-text { color: #111827; }
        .deposit-modal-subtext { color: #6b7280; }
        .deposit-modal-arrow { color: #d1d5db; }
        .card-icon-bg { background: #fef2f2; }
        .card-icon { color: var(--primary); }
        .atm-icon-bg { background: #f0fdf4; }
        .atm-icon { color: #16a34a; }
        .usdt-icon-bg { background: #fef3c7; }
        .usdt-icon { color: #d97706; }

        [data-theme="dark"] .deposit-modal-content { background: #262626; border-top: 1px solid #404040; }
        [data-theme="dark"] .deposit-modal-title { color: #f9fafb; }
        [data-theme="dark"] .deposit-modal-close { color: #6b7280; }
        [data-theme="dark"] .deposit-modal-link { border-color: #404040; color: #f9fafb; }
        [data-theme="dark"] .deposit-modal-link:hover { background: #171717; }
        [data-theme="dark"] .deposit-modal-text { color: #f9fafb; }
        [data-theme="dark"] .deposit-modal-subtext { color: #9ca3af; }
        [data-theme="dark"] .card-icon-bg { background: rgba(220, 38, 38, 0.1); }
        [data-theme="dark"] .atm-icon-bg { background: rgba(22, 163, 74, 0.1); }
    </style>

    <!-- Floating Support — chỉ hiển thị kênh nào đã được Admin cấu hình -->
    @php
        $supportZalo = trim((string) config_get('zalo', ''));
        $supportFacebook = trim((string) config_get('facebook', ''));
        $supportPhone = trim((string) config_get('phone', ''));
        $supportHasAny = $supportZalo !== '' || $supportFacebook !== '' || $supportPhone !== '';
    @endphp
    @if ($supportHasAny)
    <div class="floating-support">
        @if ($supportZalo !== '')
            <a href="{{ \Illuminate\Support\Str::startsWith($supportZalo, ['http://', 'https://']) ? $supportZalo : 'https://zalo.me/' . ltrim($supportZalo, '+ ') }}"
               target="_blank" rel="noopener noreferrer" class="support-item zalo" title="Hỗ trợ Zalo" aria-label="Hỗ trợ Zalo">
                <span class="iconify support-icon-fallback" data-icon="ant-design:message-outlined"></span>
                <span class="support-text">Chat Zalo</span>
            </a>
        @endif
        @if ($supportFacebook !== '')
            <a href="{{ \Illuminate\Support\Str::startsWith($supportFacebook, ['http://', 'https://']) ? $supportFacebook : 'https://facebook.com/' . ltrim($supportFacebook, '/') }}"
               target="_blank" rel="noopener noreferrer" class="support-item messenger" title="Hỗ trợ Facebook" aria-label="Hỗ trợ Facebook">
                <span class="iconify support-icon-fallback" data-icon="ant-design:facebook-filled"></span>
                <span class="support-text">Facebook</span>
            </a>
        @endif
        @if ($supportPhone !== '')
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $supportPhone) }}" class="support-item hotline" title="Hotline">
                <div class="phone-icon-wrapper">
                    <span class="iconify" data-icon="ant-design:phone-filled"></span>
                </div>
                <span class="support-text">{{ $supportPhone }}</span>
            </a>
        @endif
    </div>
    @endif

    <style>
        .floating-support {
            position: fixed;
            bottom: 80px;
            right: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 999;
        }
        .support-item {
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .support-item img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }
        .support-item.zalo {
            background: #fff;
        }
        .support-item.messenger {
            background: #fff;
        }
        .support-item.zalo .support-icon-fallback,
        .support-item.messenger .support-icon-fallback {
            font-size: 22px;
        }
        .support-item.zalo .support-icon-fallback {
            color: #0068ff;
        }
        .support-item.messenger .support-icon-fallback {
            color: #1877f2;
        }
        .support-item.hotline {
            background: #ef4444;
            color: white;
            animation: pulse-red 2s infinite;
        }
        .phone-icon-wrapper {
            font-size: 24px;
            display: flex;
            align-items: center;
        }
        .support-item:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .support-text {
            position: absolute;
            right: 100%;
            margin-right: 12px;
            background: #fff;
            color: #1f2937;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transform: translateX(10px);
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            pointer-events: none;
        }
        .support-text::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -4px;
            transform: translateY(-50%);
            border-width: 5px 0 5px 5px;
            border-style: solid;
            border-color: transparent transparent transparent #fff;
        }
        .support-item:hover .support-text {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        [data-theme="dark"] .support-text {
            background: #1f2937;
            color: #f3f4f6;
            border: 1px solid #374151;
        }
        [data-theme="dark"] .support-text::after {
            border-color: transparent transparent transparent #1f2937;
        }
        @media (max-width: 768px) {
            .floating-support {
                bottom: 70px;
                right: 16px;
                gap: 10px;
            }
            .support-item {
                width: 40px;
                height: 40px;
            }
            .support-item img, .phone-icon-wrapper {
                font-size: 20px;
                width: 20px;
                height: 20px;
            }
            .support-text {
                display: none;
            }
        }
    </style>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop" title="Lên đầu trang" aria-label="Lên đầu trang">
        <span class="iconify" data-icon="ant-design:arrow-up-outlined"></span>
    </button>
    
    <script src="https://cdn.jsdelivr.net/gh/lelinh014756/fui-toast-js@master/assets/js/toast@1.0.1/fuiToast.min.js"></script>
    <script src="/js/app.js"></script>
    <script src="{{ asset('assets/js/discount-code.js') }}"></script>
    <script>
    (function() {
      function hidePreloader() {
        var p = document.getElementById('pagePreloader');
        if (p && p.parentNode) {
          p.style.opacity = '0';
          setTimeout(function() {
            p.remove();
          }, 300);
        }
      }
      document.addEventListener('DOMContentLoaded', function() {
        setTimeout(hidePreloader, 100);
      });
      setTimeout(hidePreloader, 1000); // safety fallback
    })();
  </script>
    @stack('scripts')
</body>
</html>
