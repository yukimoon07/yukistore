
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="robots" content="index, follow" />

    <title>@yield('title', config_get('site_name')) - {{ config_get('site_name') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->
    <meta name="description" content="{{ config_get('site_description') }}" />
    <meta name="keywords" content="{{ config_get('site_keywords') }}" />
    <meta name="author" content="{{ config_get('site_name') }}" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="{{ config_get('site_name') }}" />
    <meta property="og:title" content="@yield('title', config_get('site_name')) - {{ config_get('site_name') }}" />
    <meta property="og:description" content="{{ config_get('site_description') }}" />
    <meta property="og:image" content="{{ config_get('site_share_image', config_get('site_logo')) }}" />
    <meta property="og:image:alt" content="{{ config_get('site_name') }}" />
    <meta property="og:locale" content="vi_VN" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="{{ url()->current() }}" />
    <meta name="twitter:title" content="@yield('title', config_get('site_name')) - {{ config_get('site_name') }}" />
    <meta name="twitter:description" content="{{ config_get('site_description') }}" />
    <meta name="twitter:image" content="{{ config_get('site_share_image', config_get('site_logo')) }}" />
    <meta name="twitter:image:alt" content="{{ config_get('site_name') }}" />

    <!-- Favicon -->
    <!-- Favicon: dùng tệp cục bộ làm fallback khi Admin chưa cấu hình -->
    @php
        $faviconUrl = config_get('site_favicon');
        $hasCustomFavicon = !empty($faviconUrl);
    @endphp
    <link rel="icon" href="{{ $hasCustomFavicon ? $faviconUrl : asset('img/favicon.svg') }}" type="image/svg+xml" />
    <link rel="shortcut icon" href="{{ $hasCustomFavicon ? $faviconUrl : asset('img/favicon.svg') }}" type="image/svg+xml" />
    @if($hasCustomFavicon)
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}" />
    @endif

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- DNS Prefetch -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Space Grotesk: display/tiêu đề + số tiền. Be Vietnam Pro: thân bài dấu tiếng Việt đầy đủ -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- antd.min.css (269KB) đã bị loại: không có component AntD nào thực sự dùng trong views
         (chỉ class trùng tên tự viết trong style block). Nếu cần dùng lại, thêm:
    <link href="https://cdn.jsdelivr.net/npm/antd@4.24.16/dist/antd.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lelinh014756/fui-toast-js@master/assets/css/toast@1.0.1/fuiToast.min.css">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/legacy-compat.css" rel="stylesheet">
    <link href="/css/modern-gaming.css" rel="stylesheet">
    <link href="/css/shopgame-theme.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
        /* Token brand — giá trị giữ ở shopgame-theme.css (load cuối) để tránh 2 nguồn sự thật */
        .brand-icon {
            background: linear-gradient(135deg, var(--color-primary, #E8B33C), var(--color-warning, #C2922A));
        }
    </style>
    <script>
        (function() {
            var savedTheme = localStorage.getItem('yuki_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            window.__defaultTheme = savedTheme;
        })();
    </script>
    @if (request()->is('/'))
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "{{ config_get('site_name', 'Shop Dịch Vụ Game') }}",
            "url": "{{ url('/') }}",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "{{ url('/') }}?q={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
        </script>
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "{{ config_get('site_name') }}",
            "url": "{{ url('/') }}",
            "logo": "{{ config_get('site_logo') }}",
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "{{ config_get('phone') }}",
                "contactType": "customer service",
                "availableLanguage": "Vietnamese"
            },
            "sameAs": [
                "{{ config_get('facebook') }}",
                "{{ config_get('youtube') }}"
            ]
        }
    </script>
    @endif

    <!-- Page-specific CSS -->
    @stack('css')
           <style>
      
        /* Ant Design Preloader */
        #global-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 999999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        #global-preloader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .ant-spin-dot-global {
            position: relative;
            display: inline-block;
            font-size: 32px;
            width: 32px;
            height: 32px;
            transform: rotate(45deg);
            animation: antGlobalRotate 1.2s infinite linear;
        }

        .ant-spin-dot-global-item {
            position: absolute;
            display: block;
            width: 14px;
            height: 14px;
            background-color: var(--ant-primary);
            border-radius: 100%;
            transform: scale(0.75);
            transform-origin: 50% 50%;
            opacity: 0.3;
            animation: antGlobalSpinMove 1s infinite linear alternate;
        }

        .ant-spin-dot-global-item:nth-child(1) {
            top: 0;
            left: 0;
        }

        .ant-spin-dot-global-item:nth-child(2) {
            top: 0;
            right: 0;
            animation-delay: 0.4s;
        }

        .ant-spin-dot-global-item:nth-child(3) {
            right: 0;
            bottom: 0;
            animation-delay: 0.8s;
        }

        .ant-spin-dot-global-item:nth-child(4) {
            bottom: 0;
            left: 0;
            animation-delay: 1.2s;
        }

        @keyframes antGlobalSpinMove {
            to {
                opacity: 1;
            }
        }

        /* Language Switcher Styling - Modern Slate */
        .ant-header-lang-dropdown {
            position: relative;
            cursor: pointer;
            user-select: none;
            z-index: 1000;
        }
        .ant-header-lang-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            height: 36px;
            box-sizing: border-box;
        }
        .ant-header-lang-trigger:hover {
            border-color: rgba(56, 189, 248, 0.35) !important;
            background: rgba(255, 255, 255, 0.12) !important;
        }
        .ant-header-lang-text {
            font-size: 13px;
            font-weight: 700;
            color: #f8fafc !important;
        }
        .ant-header-lang-dropdown .ant-dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: block !important;
            background: #0d1322 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 12px;
            box-shadow: 0 16px 36px rgba(0,0,0,0.7) !important;
            padding: 6px;
        }
        .ant-header-lang-dropdown:hover .ant-dropdown-menu,
        .ant-header-lang-dropdown.active .ant-dropdown-menu {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        .ant-dropdown-item {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 8px 14px !important;
            border-radius: 8px !important;
            transition: background 0.15s !important;
            color: #cbd5e1 !important;
        }
        .ant-dropdown-item:hover,
        .ant-dropdown-item.active {
            background: rgba(232, 179, 60, 0.12) !important;
            color: var(--yuki-gold, #E8B33C) !important;
        }
    </style>
    <script>
        function openAuthModal(panel) {
            var ov = document.getElementById('authOverlay');
            var md = document.getElementById('authModal');
            if (!ov || !md) { window.location.href = '/login'; return; }
            ov.style.display = 'block';
            md.style.display = 'block';
            switchAuthPanel(panel || 'login');
            document.body.style.overflow = 'hidden';
        }
        function closeAuthModal() {
            var ov = document.getElementById('authOverlay');
            var md = document.getElementById('authModal');
            if (ov) ov.style.display = 'none';
            if (md) md.style.display = 'none';
            document.body.style.overflow = '';
        }
        function switchAuthPanel(panel) {
            document.querySelectorAll('.auth-panel').forEach(function (p) { p.style.display = 'none'; });
            var id = panel === 'register' ? 'authRegisterPanel' : panel === 'forgot' ? 'authForgotPanel' : 'authLoginPanel';
            var el = document.getElementById(id);
            if (el) el.style.display = 'block';
        }

        // Preloader Logic
        window.addEventListener('load', function () {
            var pl = document.getElementById('global-preloader');
            if (pl) pl.classList.add('hidden');
        });

        // Native Fast Language Switching (VI / EN)
        function setLanguage(lang) {
            var currentUrl = window.location.href;
            window.location.href = '/language/' + lang + '?redirect=' + encodeURIComponent(currentUrl);
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Dropdown Toggle Logic
            const dropdown = document.querySelector('.ant-header-lang-dropdown');
            if (dropdown) {
                dropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('active');
                });
                document.addEventListener('click', () => {
                    dropdown.classList.remove('active');
                });
            }
        });
    </script>
</head>

