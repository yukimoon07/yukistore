@php
    $currentLocale = app()->getLocale();
    $isEn = ($currentLocale === 'en');
@endphp

<!-- Preloader -->
<div id="pagePreloader" style="position:fixed;inset:0;z-index:99999;background:rgba(11,15,25,0.75);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;transition:opacity .3s ease;">
  <span class="antd-spin"><i></i><i></i><i></i><i></i></span>
</div>
<style>
  .antd-spin {
    display: inline-block;
    width: 32px;
    height: 32px;
    position: relative;
    animation: antdSpinRotate 1.2s linear infinite;
  }

  .antd-spin i {
    position: absolute;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--color-primary, #E8B33C);
    opacity: .3;
    animation: antdSpinDot 1s ease-in-out infinite;
  }

  .antd-spin i:nth-child(1) { top: 0; left: 50%; margin-left: -5px; animation-delay: 0s; }
  .antd-spin i:nth-child(2) { right: 0; top: 50%; margin-top: -5px; animation-delay: .4s; }
  .antd-spin i:nth-child(3) { bottom: 0; left: 50%; margin-left: -5px; animation-delay: .8s; }
  .antd-spin i:nth-child(4) { left: 0; top: 50%; margin-top: -5px; animation-delay: 1.2s; }

  @keyframes antdSpinRotate { to { transform: rotate(360deg); } }
  @keyframes antdSpinDot {
    0%, 100% { opacity: .3; transform: scale(.6); }
    50% { opacity: 1; transform: scale(1); }
  }

  /* ==========================================================
     MODERN GAMING NAVBAR - UNIFIED DARK SLATE PALETTE
     ========================================================== */
  .navbar {
      height: 64px !important;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      background: rgba(11, 15, 25, 0.94) !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
  }
  .nav-container {
      max-width: 1320px !important;
      margin: 0 auto;
      padding: 0 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 64px;
      gap: 8px;
  }
  .nav-brand {
      display: inline-flex;
      align-items: center;
      flex-shrink: 0;
      max-width: 160px;
      overflow: hidden;
      white-space: nowrap;
      text-decoration: none;
  }
  .nav-user {
      flex-shrink: 0 !important;
      display: flex;
      align-items: center;
      gap: 8px;
  }

  @media (min-width: 992px) {
      .nav-toggle {
          display: none !important;
      }
      .mobile-only-admin {
          display: none !important;
      }
      .nav-links {
          display: flex !important;
          align-items: center !important;
          gap: 2px !important;
          list-style: none !important;
          margin: 0 !important;
          padding: 0 !important;
          flex-wrap: nowrap !important;
          flex-shrink: 1 !important;
      }
      .nav-links li {
          flex-shrink: 0 !important;
          border-bottom: none !important;
      }
      /* Uniform Navbar Links Color & Typography */
      .nav-links a.nav-link-item,
      .nav-links .nav-link-item {
          display: inline-flex !important;
          align-items: center !important;
          gap: 5px !important;
          padding: 7px 9px !important;
          border-radius: 8px !important;
          font-weight: 600 !important;
          font-size: 0.84rem !important;
          white-space: nowrap !important;
          line-height: 1.2 !important;
          text-decoration: none !important;
          color: #cbd5e1 !important;
          transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
          cursor: pointer;
      }
      .nav-links a.nav-link-item:hover,
      .nav-links .nav-link-item:hover,
      .nav-links a.nav-link-item.active {
          color: var(--yuki-gold, #E8B33C) !important;
          background: rgba(255, 255, 255, 0.06) !important;
      }
      .nav-links a.nav-link-item.active {
          color: var(--yuki-gold, #E8B33C) !important;
      }

      /* ----------------------------------------------------------
         MASTER-DETAIL CATEGORY SUBMENU (SPLIT PANE)
         ---------------------------------------------------------- */
      .nav-categories-dropdown {
          position: relative !important;
      }
      /* Invisible hit area bridge so mouse hover never drops out */
      .nav-categories-dropdown::after {
          content: '';
          position: absolute;
          top: 100%;
          left: 0;
          width: 100%;
          height: 18px;
          display: block;
          z-index: 99998;
      }
      .nav-cat-dropdown-panel {
          display: none;
          position: absolute;
          top: 100%;
          left: 0;
          width: 780px;
          max-width: calc(100vw - 32px);
          box-sizing: border-box !important;
          background: #0d1322 !important;
          border: 1px solid rgba(255, 255, 255, 0.12) !important;
          border-radius: 14px !important;
          box-shadow: 0 25px 60px rgba(0, 0, 0, 0.85), 0 0 1px 1px rgba(255, 255, 255, 0.05) !important;
          backdrop-filter: blur(24px);
          -webkit-backdrop-filter: blur(24px);
          z-index: 99999 !important;
          overflow: hidden;
          pointer-events: auto !important;
      }
      .nav-categories-dropdown:hover .nav-cat-dropdown-panel,
      .nav-categories-dropdown.open .nav-cat-dropdown-panel {
          display: flex !important;
          animation: catPanelFadeIn 0.18s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      }
      .nav-categories-dropdown:hover .nav-arrow,
      .nav-categories-dropdown.open .nav-arrow {
          transform: rotate(180deg);
      }
      @keyframes catPanelFadeIn {
          from { opacity: 0; transform: translateY(6px); }
          to { opacity: 1; transform: translateY(0); }
      }

      /* Left Side: Category Tabs (5 groups) */
      .nav-cat-tabs {
          width: 220px;
          box-sizing: border-box !important;
          background: rgba(0, 0, 0, 0.35) !important;
          border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
          padding: 12px 8px;
          display: flex;
          flex-direction: column;
          gap: 4px;
          flex-shrink: 0;
      }
      .cat-tab-item {
          display: flex;
          align-items: center;
          gap: 8px;
          padding: 10px 10px;
          border-radius: 10px;
          text-decoration: none;
          cursor: pointer;
          transition: all 0.15s ease;
          border: 1px solid transparent;
          user-select: none;
          box-sizing: border-box !important;
      }
      .cat-tab-item:hover,
      .cat-tab-item.active {
          background: rgba(232, 179, 60, 0.12) !important;
          border-color: rgba(232, 179, 60, 0.3) !important;
      }
      .cat-tab-icon {
          width: 26px;
          height: 26px;
          border-radius: 7px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 13px;
          flex-shrink: 0;
      }
      .cat-tab-name {
          flex-grow: 1;
          white-space: nowrap;
          font-size: 0.86rem !important;
          font-weight: 600 !important;
          color: #e2e8f0 !important; /* Crystal clear high-contrast white */
          line-height: 1.2 !important;
          transition: color 0.15s ease !important;
      }
      .cat-tab-item:hover .cat-tab-name,
      .cat-tab-item.active .cat-tab-name {
          color: var(--yuki-gold, #E8B33C) !important;
      }
      .cat-tab-count {
          font-size: 0.68rem;
          font-weight: 700;
          padding: 2px 6px;
          border-radius: 10px;
          flex-shrink: 0;
      }
      .cat-tab-arrow {
          font-size: 0.7rem;
          color: #64748b;
          transition: transform 0.2s, color 0.2s;
          display: inline-flex;
          align-items: center;
          flex-shrink: 0;
      }
      .cat-tab-item:hover .cat-tab-arrow,
      .cat-tab-item.active .cat-tab-arrow {
          transform: translateX(3px);
          color: var(--yuki-gold, #E8B33C);
      }

      /* Right Side: Detail Content Panes */
      .nav-cat-content {
          flex: 1 1 0;
          min-width: 0;
          width: auto;
          box-sizing: border-box !important;
          padding: 14px 18px;
          background: #0d1322 !important;
          min-height: 280px;
          max-height: 440px;
          overflow-y: auto;
      }
      .nav-cat-content::-webkit-scrollbar {
          width: 6px;
      }
      .nav-cat-content::-webkit-scrollbar-thumb {
          background: rgba(255, 255, 255, 0.15);
          border-radius: 4px;
      }
      .cat-detail-panel {
          display: none;
          width: 100%;
          box-sizing: border-box !important;
      }
      .cat-detail-panel.active {
          display: block;
          animation: panelFade 0.15s ease-out;
      }
      @keyframes panelFade {
          from { opacity: 0; }
          to { opacity: 1; }
      }
      .cat-detail-header {
          display: flex;
          align-items: center;
          gap: 8px;
          padding-bottom: 10px;
          margin-bottom: 12px;
          border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      }
      .cat-detail-title {
          font-size: 0.88rem !important;
          font-weight: 800 !important;
          color: #f8fafc !important;
          text-transform: uppercase !important;
          letter-spacing: 0.5px !important;
      }
      .cat-detail-subtitle {
          font-size: 0.76rem !important;
          color: #94a3b8 !important;
          font-weight: 500 !important;
          margin-left: 2px;
      }
      .cat-items-grid {
          display: grid;
          grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
          gap: 8px 10px;
          width: 100% !important;
          box-sizing: border-box !important;
      }
      .cat-item-card {
          display: flex !important;
          align-items: center !important;
          gap: 8px !important;
          padding: 8px 10px !important;
          border-radius: 9px !important;
          background: rgba(255, 255, 255, 0.04) !important;
          border: 1px solid rgba(255, 255, 255, 0.08) !important;
          text-decoration: none !important;
          transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1) !important;
          cursor: pointer !important;
          width: 100% !important;
          min-width: 0 !important;
          box-sizing: border-box !important;
          overflow: hidden !important;
      }
      .cat-item-card:hover {
          background: rgba(232, 179, 60, 0.12) !important;
          border-color: rgba(232, 179, 60, 0.35) !important;
          transform: translateY(-1px) !important;
      }
      .cat-item-thumb {
          width: 26px !important;
          height: 26px !important;
          border-radius: 6px !important;
          object-fit: cover !important;
          flex-shrink: 0 !important;
          background: #1e293b !important;
          border: 1px solid rgba(255, 255, 255, 0.1) !important;
      }
      .cat-item-thumb-fallback {
          width: 26px !important;
          height: 26px !important;
          border-radius: 6px !important;
          background: rgba(255, 255, 255, 0.06) !important;
          display: flex !important;
          align-items: center !important;
          justify-content: center !important;
          font-size: 13px !important;
          color: #94a3b8 !important;
          flex-shrink: 0 !important;
      }
      .cat-item-name {
          flex: 1 1 auto !important;
          min-width: 0 !important;
          font-size: 0.82rem !important;
          font-weight: 600 !important;
          color: #f1f5f9 !important; /* Fully visible, high contrast text */
          line-height: 1.3 !important;
          white-space: normal !important;
          word-break: break-word !important;
          overflow-wrap: break-word !important;
          transition: color 0.15s ease !important;
      }
      .cat-item-card:hover .cat-item-name {
          color: var(--yuki-gold, #E8B33C) !important;
      }
      .cat-item-badge {
          font-size: 0.65rem !important;
          font-weight: 800 !important;
          padding: 2px 6px !important;
          border-radius: 4px !important;
          line-height: 1 !important;
          flex-shrink: 0 !important;
          letter-spacing: 0.4px !important;
          white-space: nowrap !important;
      }
  }

  @media (min-width: 992px) and (max-width: 1260px) {
      .nav-container { padding: 0 8px !important; gap: 4px !important; }
      .nav-links { gap: 1px !important; }
      .nav-links a.nav-link-item,
      .nav-links .nav-link-item {
          padding: 5px 6px !important;
          font-size: 0.78rem !important;
          gap: 3px !important;
      }
      .nav-brand { max-width: 120px !important; }
      .btn-admin-panel { padding: 5px 9px !important; font-size: 0.75rem !important; }
      .btn-deposit-glow { padding: 5px 10px !important; font-size: 0.78rem !important; }
  }

  @media (max-width: 991px) {
      .mobile-only-admin {
          display: block !important;
          margin-top: 10px !important;
          padding-top: 10px !important;
          border-top: 1px solid rgba(245, 158, 11, 0.25) !important;
      }
      .btn-admin-panel {
          display: none !important;
      }
      .nav-toggle {
          display: flex !important;
          order: 3;
          flex-direction: column;
          gap: 5px;
          background: transparent;
          border: none;
          cursor: pointer;
          padding: 8px;
      }
      .nav-toggle span {
          display: block;
          width: 22px;
          height: 2px;
          background: #f8fafc;
          border-radius: 2px;
          transition: all 0.3s;
      }
      .nav-links {
          display: none !important;
          position: fixed;
          top: 0;
          right: 0;
          bottom: 0;
          width: 300px;
          background: #0d1322 !important;
          border-left: 1px solid rgba(255,255,255,0.1);
          flex-direction: column;
          padding: 20px;
          z-index: 10000;
          overflow-y: auto;
          gap: 8px !important;
      }
      .nav-links.show {
          display: flex !important;
      }
      .nav-offcanvas-header {
          display: flex !important;
          align-items: center;
          justify-content: space-between;
          width: 100%;
          padding-bottom: 16px;
          margin-bottom: 12px;
          border-bottom: 1px solid rgba(255,255,255,0.08);
      }
      .nav-close {
          background: transparent;
          border: none;
          color: #94a3b8;
          font-size: 20px;
          cursor: pointer;
      }
      .nav-categories-dropdown {
          position: relative !important;
      }
      .nav-cat-dropdown-panel {
          position: static !important;
          display: flex !important;
          flex-direction: column !important;
          width: 100% !important;
          background: transparent !important;
          border: none !important;
          box-shadow: none !important;
          padding: 0 !important;
          border-radius: 0 !important;
      }
      .nav-cat-tabs {
          width: 100% !important;
          border-right: none !important;
          background: transparent !important;
          padding: 4px 0 !important;
          display: none !important;
      }
      .nav-cat-content {
          padding: 6px 0 !important;
          min-height: 0 !important;
          max-height: none !important;
          background: transparent !important;
          overflow-y: visible !important;
      }
      .cat-detail-panel {
          display: block !important;
          margin-bottom: 14px !important;
          background: rgba(255, 255, 255, 0.03) !important;
          padding: 12px 10px !important;
          border-radius: 10px !important;
      }
      .cat-items-grid {
          grid-template-columns: 1fr !important;
          gap: 6px !important;
      }
      .cat-item-card {
          padding: 9px 10px !important;
          font-size: 0.84rem !important;
      }
  }

  .nav-dropdown-menu {
      background: #0d1322 !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      border-radius: 10px !important;
      box-shadow: 0 16px 36px rgba(0,0,0,0.6) !important;
      padding: 6px !important;
  }
  .nav-dropdown-menu a {
      color: #cbd5e1 !important;
      padding: 8px 14px !important;
      border-radius: 6px !important;
      display: flex !important;
      align-items: center !important;
      gap: 8px !important;
      font-size: 0.85rem !important;
      font-weight: 500 !important;
      text-decoration: none !important;
      transition: background 0.15s !important;
  }
  .nav-dropdown-menu a:hover {
      background: rgba(255, 255, 255, 0.06) !important;
      color: var(--yuki-gold, #E8B33C) !important;
  }

  /* LANGUAGE SWITCHER - DARK SLATE GLASS BUTTON */
  .ant-header-lang-trigger {
      display: inline-flex !important;
      align-items: center !important;
      gap: 8px !important;
      padding: 6px 12px !important;
      border-radius: 20px !important;
      background: rgba(255, 255, 255, 0.06) !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      color: #f8fafc !important;
      cursor: pointer !important;
      user-select: none !important;
      transition: all 0.2s ease !important;
      height: 34px !important;
      box-sizing: border-box !important;
  }
  .ant-header-lang-trigger:hover {
      background: rgba(255, 255, 255, 0.12) !important;
      border-color: rgba(232, 179, 60, 0.35) !important;
  }
  .ant-header-lang-text {
      font-size: 13px !important;
      font-weight: 700 !important;
      color: #f8fafc !important;
  }
  .ant-header-lang-dropdown .ant-dropdown-menu {
      display: none;
      position: absolute !important;
      z-index: 1000 !important;
      width: 145px !important;
      right: 0 !important;
      margin-top: 6px !important;
      background: #0d1322 !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      border-radius: 12px !important;
      box-shadow: 0 16px 36px rgba(0,0,0,0.7) !important;
      padding: 6px !important;
  }
  .ant-header-lang-dropdown:hover .ant-dropdown-menu,
  .ant-header-lang-dropdown.active .ant-dropdown-menu {
      display: block !important;
  }
  .ant-dropdown-item {
      display: flex !important;
      align-items: center !important;
      gap: 10px !important;
      padding: 8px 12px !important;
      border-radius: 8px !important;
      transition: background 0.15s !important;
      color: #cbd5e1 !important;
      cursor: pointer !important;
  }
  .ant-dropdown-item:hover,
  .ant-dropdown-item.active {
      background: rgba(232, 179, 60, 0.12) !important;
      color: var(--yuki-gold, #E8B33C) !important;
  }
</style>

<nav class="navbar">
    <div class="nav-container">
        <!-- Logo -->
        <a href="/" class="nav-brand">
            <img src="{{ config_get('site_logo') ?: '/img/logo.svg' }}"
                 alt="{{ config_get('site_name', 'ShopGame') }}"
                 onerror="if(!this.dataset.failed){this.dataset.failed=1;this.src='/img/logo.svg';}else{this.style.display='none';var fb=this.parentNode.querySelector('.fallback-brand');if(fb)fb.style.display='inline-flex';}"
                 width="120" height="32"
                 style="height:32px; max-width:160px; width:auto; object-fit:contain; display:block;">
            <span class="fallback-brand" style="display:none; align-items:center; gap:8px; font-size:1.1rem; font-weight:800; color:#f8fafc; white-space:nowrap;">
                <span class="brand-icon" style="width:28px; height:28px; border-radius:6px; background:linear-gradient(135deg, #E8B33C, #C2922A); display:inline-flex; align-items:center; justify-content:center; color:#fff; font-size:0.85rem;"><i class="fa-solid fa-gamepad"></i></span>
                <span>{{ \Illuminate\Support\Str::limit(config_get('site_name', 'SHOP GAME'), 16) }}</span>
            </span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" onclick="toggleNav()">
            <span></span><span></span><span></span>
        </button>

        <!-- Navigation Links -->
        <ul class="nav-links" id="navLinks">
            <li class="nav-offcanvas-header" style="display:none;">
                <a href="/" class="nav-brand" style="max-width: 160px; overflow: hidden;">
                    <img src="{{ config_get('site_logo') ?: '/img/logo.svg' }}"
                         alt="{{ config_get('site_name', 'ShopGame') }}"
                         width="105" height="28"
                         style="height:28px; max-width:130px; width:auto; object-fit:contain; display:block;">
                </a>
                <button class="nav-close" id="navClose" onclick="closeNav()" aria-label="Close mobile menu">
                    <span class="iconify" data-icon="ant-design:close-outlined"></span>
                </button>
            </li>
            <li><a href="/" class="nav-link-item {{ request()->is('/') ? 'active' : '' }}"><span class="iconify"
                        data-icon="ant-design:home-outlined"></span> {{ __('shop.nav_home') }}</a></li>
            <li><a href="/service" class="nav-link-item {{ request()->is('service*') ? 'active' : '' }}"><span class="iconify"
                        data-icon="ant-design:thunderbolt-outlined"></span> {{ __('shop.nav_services') }}</a></li>
            
            <!-- MASTER-DETAIL SUBMENU: DANH MỤC -->
            <li class="nav-categories-dropdown" id="navCategoriesDropdown">
                <a href="javascript:void(0)" class="nav-link-item" id="navCategoriesTrigger"><span class="iconify"
                        data-icon="ant-design:appstore-outlined"></span> {{ __('shop.nav_categories') }} <span class="iconify nav-arrow"
                        data-icon="ant-design:down-outlined" style="font-size:0.65rem;"></span></a>
                
                @php
                    $navCategories = \App\Models\Category::where('active', 1)->get();
                    $navRandomCategories = \App\Models\RandomCategory::where('active', 1)->get();
                    $navServices = \App\Models\GameService::where('active', 1)->get();

                    // 1. Cày Thuê (Game Boosting)
                    $groupBoosting = $navServices;

                    // 2. Acc Liên Quân (Arena of Valor)
                    $groupLienQuan = collect();
                    foreach($navRandomCategories as $rcat) {
                        if ($rcat->game_group_id == 1 || str_contains($rcat->slug, 'lien-quan') || str_contains($rcat->slug, 'lq')) {
                            $badge = null;
                            if (str_contains($rcat->slug, '2k')) $badge = '2K';
                            elseif (str_contains($rcat->slug, '20k')) $badge = '20K';
                            elseif (str_contains($rcat->slug, '50k')) $badge = '50K';
                            elseif (str_contains($rcat->slug, '99k')) $badge = '99K';
                            elseif (str_contains($rcat->slug, '100k')) $badge = '100K';
                            elseif (str_contains($rcat->slug, '200k')) $badge = '200K';

                            $groupLienQuan->push([
                                'name' => $rcat->name,
                                'url' => route('random.index', ['slug' => $rcat->slug]),
                                'thumbnail' => $rcat->thumbnail,
                                'badge' => $badge,
                                'badge_style' => 'background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.4);',
                                'fallback_icon' => 'ant-design:gift-outlined',
                            ]);
                        }
                    }
                    foreach($navCategories as $cat) {
                        if ($cat->game_group_id == 1 || str_contains($cat->slug, 'lien-quan')) {
                            $badge = null;
                            if (str_contains($cat->slug, 'trang-thong-tin')) $badge = 'TTT';
                            elseif (str_contains($cat->slug, 'reg')) $badge = 'REG';
                            elseif (str_contains($cat->slug, 'skin')) $badge = 'SKIN';

                            $groupLienQuan->push([
                                'name' => $cat->name,
                                'url' => route('category.index', ['slug' => $cat->slug]),
                                'thumbnail' => $cat->thumbnail,
                                'badge' => $badge,
                                'badge_style' => 'background: rgba(56, 189, 248, 0.2); color: var(--yuki-gold, #E8B33C); border: 1px solid rgba(56, 189, 248, 0.4);',
                                'fallback_icon' => 'ant-design:user-outlined',
                            ]);
                        }
                    }

                    // 3. Acc Roblox
                    $groupRoblox = collect();
                    foreach($navCategories as $cat) {
                        if ($cat->game_group_id == 3 || str_contains($cat->slug, 'blox') || str_contains($cat->slug, 'egg') || str_contains($cat->slug, 'roblox')) {
                            $groupRoblox->push([
                                'name' => $cat->name,
                                'url' => route('category.index', ['slug' => $cat->slug]),
                                'thumbnail' => $cat->thumbnail,
                                'badge' => str_contains($cat->slug, 'blox') ? 'HOT' : (str_contains($cat->slug, 'egg') ? 'EGG' : null),
                                'badge_style' => 'background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.4);',
                                'fallback_icon' => 'ant-design:block-outlined',
                            ]);
                        }
                    }
                    foreach($navRandomCategories as $rcat) {
                        if ($rcat->game_group_id == 3 || str_contains($rcat->slug, 'blox') || str_contains($rcat->slug, 'roblox')) {
                            $groupRoblox->push([
                                'name' => $rcat->name,
                                'url' => route('random.index', ['slug' => $rcat->slug]),
                                'thumbnail' => $rcat->thumbnail,
                                'badge' => '99K',
                                'badge_style' => 'background: rgba(168, 85, 247, 0.2); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.4);',
                                'fallback_icon' => 'ant-design:gift-outlined',
                            ]);
                        }
                    }

                    // 4. Acc Genshin
                    $groupGenshin = collect();
                    $sortedGenshin = $navCategories->filter(function($c) {
                        return $c->game_group_id == 4 || str_contains($c->slug, 'genshin');
                    })->sortBy(function($c) {
                        return str_contains($c->slug, 'reroll') ? 1 : 2;
                    });
                    foreach($sortedGenshin as $cat) {
                        $groupGenshin->push([
                            'name' => $cat->name,
                            'url' => route('category.index', ['slug' => $cat->slug]),
                            'thumbnail' => $cat->thumbnail,
                            'badge' => str_contains($cat->slug, 'vip') ? 'VIP' : 'LUCK',
                            'badge_style' => 'background: rgba(236, 72, 153, 0.2); color: #ec4899; border: 1px solid rgba(236, 72, 153, 0.4);',
                            'fallback_icon' => 'ant-design:star-outlined',
                        ]);
                    }

                    // 5. Acc FCO / FCM
                    $groupFC = collect();
                    $sortedFC = $navCategories->filter(function($c) {
                        return $c->game_group_id == 5 || str_contains($c->slug, 'fc') || str_contains($c->slug, 'fifa');
                    })->sortBy(function($c) {
                        return str_contains($c->slug, 'mobile') ? 1 : 2;
                    });
                    foreach($sortedFC as $cat) {
                        $groupFC->push([
                            'name' => $cat->name,
                            'url' => route('category.index', ['slug' => $cat->slug]),
                            'thumbnail' => $cat->thumbnail,
                            'badge' => str_contains($cat->slug, 'mobile') ? 'FCM' : 'FO4',
                            'badge_style' => 'background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.4);',
                            'fallback_icon' => 'ant-design:dribbble-circle-outlined',
                        ]);
                    }
                @endphp

                <!-- Unified Master-Detail Dropdown Panel -->
                <div class="nav-cat-dropdown-panel" id="navCatDropdownPanel">
                    <!-- Left: Category Tabs (5 Groups) -->
                    <div class="nav-cat-tabs">
                        <div class="cat-tab-item active" data-target="cat-panel-boosting" onmouseenter="switchNavCatTab(this, 'cat-panel-boosting')" onclick="switchNavCatTab(this, 'cat-panel-boosting')">
                            <span class="cat-tab-icon" style="background: rgba(232, 179, 60, 0.15); color: #C2922A;">
                                <span class="iconify" data-icon="ant-design:thunderbolt-filled"></span>
                            </span>
                            <span class="cat-tab-name">{{ __('shop.nav_cat_boosting') }}</span>
                            <span class="cat-tab-count" style="background: rgba(232, 179, 60, 0.15); color: var(--yuki-gold, #E8B33C);">{{ $groupBoosting->count() }}</span>
                            <span class="cat-tab-arrow"><span class="iconify" data-icon="ant-design:right-outlined"></span></span>
                        </div>

                        <div class="cat-tab-item" data-target="cat-panel-lienquan" onmouseenter="switchNavCatTab(this, 'cat-panel-lienquan')" onclick="switchNavCatTab(this, 'cat-panel-lienquan')">
                            <span class="cat-tab-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                                <span class="iconify" data-icon="ant-design:trophy-filled"></span>
                            </span>
                            <span class="cat-tab-name">{{ __('shop.nav_cat_lienquan') }}</span>
                            <span class="cat-tab-count" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">{{ $groupLienQuan->count() }}</span>
                            <span class="cat-tab-arrow"><span class="iconify" data-icon="ant-design:right-outlined"></span></span>
                        </div>

                        <div class="cat-tab-item" data-target="cat-panel-roblox" onmouseenter="switchNavCatTab(this, 'cat-panel-roblox')" onclick="switchNavCatTab(this, 'cat-panel-roblox')">
                            <span class="cat-tab-icon" style="background: rgba(168, 85, 247, 0.15); color: #a855f7;">
                                <span class="iconify" data-icon="ant-design:block-outlined"></span>
                            </span>
                            <span class="cat-tab-name">{{ __('shop.nav_cat_roblox') }}</span>
                            <span class="cat-tab-count" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">{{ $groupRoblox->count() }}</span>
                            <span class="cat-tab-arrow"><span class="iconify" data-icon="ant-design:right-outlined"></span></span>
                        </div>

                        <div class="cat-tab-item" data-target="cat-panel-genshin" onmouseenter="switchNavCatTab(this, 'cat-panel-genshin')" onclick="switchNavCatTab(this, 'cat-panel-genshin')">
                            <span class="cat-tab-icon" style="background: rgba(236, 72, 153, 0.15); color: #ec4899;">
                                <span class="iconify" data-icon="ant-design:star-filled"></span>
                            </span>
                            <span class="cat-tab-name">{{ __('shop.nav_cat_genshin') }}</span>
                            <span class="cat-tab-count" style="background: rgba(236, 72, 153, 0.15); color: #f472b6;">{{ $groupGenshin->count() }}</span>
                            <span class="cat-tab-arrow"><span class="iconify" data-icon="ant-design:right-outlined"></span></span>
                        </div>

                        <div class="cat-tab-item" data-target="cat-panel-fc" onmouseenter="switchNavCatTab(this, 'cat-panel-fc')" onclick="switchNavCatTab(this, 'cat-panel-fc')">
                            <span class="cat-tab-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                <span class="iconify" data-icon="ant-design:dribbble-circle-filled"></span>
                            </span>
                            <span class="cat-tab-name">{{ __('shop.nav_cat_fc') }}</span>
                            <span class="cat-tab-count" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">{{ $groupFC->count() }}</span>
                            <span class="cat-tab-arrow"><span class="iconify" data-icon="ant-design:right-outlined"></span></span>
                        </div>
                    </div>

                    <!-- Right: Content Panes -->
                    <div class="nav-cat-content">
                        <!-- 1. CÀY THUÊ -->
                        <div class="cat-detail-panel active" id="cat-panel-boosting">
                            <div class="cat-detail-header">
                                <span class="iconify" data-icon="ant-design:thunderbolt-filled" style="color: #C2922A;"></span>
                                <span class="cat-detail-title" style="color: var(--yuki-gold, #E8B33C);">{{ __('shop.nav_cat_boosting') }}</span>
                                <span class="cat-detail-subtitle">({{ $groupBoosting->count() }} {{ __('shop.nav_cat_count_services', ['count' => '']) }})</span>
                            </div>
                            <div class="cat-items-grid">
                                @foreach($groupBoosting as $service)
                                @php
                                    $sBadge = null;
                                    $sBadgeStyle = '';
                                    if (str_contains($service->slug, 'blox')) {
                                        $sBadge = 'HOT';
                                        $sBadgeStyle = 'background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.4);';
                                    } elseif (str_contains($service->slug, 'genshin')) {
                                        $sBadge = 'PRO';
                                        $sBadgeStyle = 'background: rgba(56, 189, 248, 0.2); color: var(--yuki-gold, #E8B33C); border: 1px solid rgba(56, 189, 248, 0.4);';
                                    } elseif (str_contains($service->slug, 'ride')) {
                                        $sBadge = 'PET';
                                        $sBadgeStyle = 'background: rgba(168, 85, 247, 0.2); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.4);';
                                    }
                                @endphp
                                <a href="{{ route('service.show', ['slug' => $service->slug]) }}" class="cat-item-card">
                                    @if($service->thumbnail)
                                    <img src="{{ $service->thumbnail }}" alt="" class="cat-item-thumb">
                                    @else
                                    <span class="cat-item-thumb-fallback"><span class="iconify" data-icon="ant-design:tool-outlined"></span></span>
                                    @endif
                                    <span class="cat-item-name">{{ $service->name }}</span>
                                    @if($sBadge)
                                    <span class="cat-item-badge" style="{{ $sBadgeStyle }}">{{ $sBadge }}</span>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- 2. ACC LIÊN QUÂN -->
                        <div class="cat-detail-panel" id="cat-panel-lienquan">
                            <div class="cat-detail-header">
                                <span class="iconify" data-icon="ant-design:trophy-filled" style="color: #f59e0b;"></span>
                                <span class="cat-detail-title" style="color: #fbbf24;">{{ __('shop.nav_cat_lienquan') }}</span>
                                <span class="cat-detail-subtitle">({{ $groupLienQuan->count() }} {{ __('shop.nav_cat_count_categories', ['count' => '']) }})</span>
                            </div>
                            <div class="cat-items-grid">
                                @foreach($groupLienQuan as $item)
                                <a href="{{ $item['url'] }}" class="cat-item-card">
                                    @if($item['thumbnail'])
                                    <img src="{{ $item['thumbnail'] }}" alt="" class="cat-item-thumb">
                                    @else
                                    <span class="cat-item-thumb-fallback"><span class="iconify" data-icon="{{ $item['fallback_icon'] }}"></span></span>
                                    @endif
                                    <span class="cat-item-name">{{ $item['name'] }}</span>
                                    @if($item['badge'])
                                    <span class="cat-item-badge" style="{{ $item['badge_style'] }}">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- 3. ACC ROBLOX -->
                        <div class="cat-detail-panel" id="cat-panel-roblox">
                            <div class="cat-detail-header">
                                <span class="iconify" data-icon="ant-design:block-outlined" style="color: #a855f7;"></span>
                                <span class="cat-detail-title" style="color: #c084fc;">{{ __('shop.nav_cat_roblox') }}</span>
                                <span class="cat-detail-subtitle">({{ $groupRoblox->count() }} {{ __('shop.nav_cat_count_categories', ['count' => '']) }})</span>
                            </div>
                            <div class="cat-items-grid">
                                @foreach($groupRoblox as $item)
                                <a href="{{ $item['url'] }}" class="cat-item-card">
                                    @if($item['thumbnail'])
                                    <img src="{{ $item['thumbnail'] }}" alt="" class="cat-item-thumb">
                                    @else
                                    <span class="cat-item-thumb-fallback"><span class="iconify" data-icon="{{ $item['fallback_icon'] }}"></span></span>
                                    @endif
                                    <span class="cat-item-name">{{ $item['name'] }}</span>
                                    @if($item['badge'])
                                    <span class="cat-item-badge" style="{{ $item['badge_style'] }}">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- 4. ACC GENSHIN -->
                        <div class="cat-detail-panel" id="cat-panel-genshin">
                            <div class="cat-detail-header">
                                <span class="iconify" data-icon="ant-design:star-filled" style="color: #ec4899;"></span>
                                <span class="cat-detail-title" style="color: #f472b6;">{{ __('shop.nav_cat_genshin') }}</span>
                                <span class="cat-detail-subtitle">({{ $groupGenshin->count() }} {{ __('shop.nav_cat_count_categories', ['count' => '']) }})</span>
                            </div>
                            <div class="cat-items-grid">
                                @foreach($groupGenshin as $item)
                                <a href="{{ $item['url'] }}" class="cat-item-card">
                                    @if($item['thumbnail'])
                                    <img src="{{ $item['thumbnail'] }}" alt="" class="cat-item-thumb">
                                    @else
                                    <span class="cat-item-thumb-fallback"><span class="iconify" data-icon="{{ $item['fallback_icon'] }}"></span></span>
                                    @endif
                                    <span class="cat-item-name">{{ $item['name'] }}</span>
                                    @if($item['badge'])
                                    <span class="cat-item-badge" style="{{ $item['badge_style'] }}">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- 5. ACC FCO / FCM -->
                        <div class="cat-detail-panel" id="cat-panel-fc">
                            <div class="cat-detail-header">
                                <span class="iconify" data-icon="ant-design:dribbble-circle-filled" style="color: #10b981;"></span>
                                <span class="cat-detail-title" style="color: #34d399;">{{ __('shop.nav_cat_fc') }}</span>
                                <span class="cat-detail-subtitle">({{ $groupFC->count() }} {{ __('shop.nav_cat_count_categories', ['count' => '']) }})</span>
                            </div>
                            <div class="cat-items-grid">
                                @foreach($groupFC as $item)
                                <a href="{{ $item['url'] }}" class="cat-item-card">
                                    @if($item['thumbnail'])
                                    <img src="{{ $item['thumbnail'] }}" alt="" class="cat-item-thumb">
                                    @else
                                    <span class="cat-item-thumb-fallback"><span class="iconify" data-icon="{{ $item['fallback_icon'] }}"></span></span>
                                    @endif
                                    <span class="cat-item-name">{{ $item['name'] }}</span>
                                    @if($item['badge'])
                                    <span class="cat-item-badge" style="{{ $item['badge_style'] }}">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </li>

            <!-- Deposit Dropdown -->
            <li class="nav-dropdown">
                <a href="javascript:void(0)" class="nav-link-item"><span class="iconify"
                        data-icon="ant-design:dollar-outlined"></span> {{ __('shop.nav_deposit') }} <span class="iconify nav-arrow"
                        data-icon="ant-design:down-outlined" style="font-size:0.65rem;"></span></a>
                <ul class="nav-dropdown-menu">
                    <li><a href="{{ route('profile.deposit-card') }}"><span class="iconify"
                                data-icon="ant-design:credit-card-outlined"></span> {{ __('shop.nav_deposit_card') }}</a></li>
                    <li><a href="{{ route('profile.deposit-atm') }}"><span class="iconify"
                                data-icon="ant-design:bank-outlined"></span> {{ __('shop.nav_deposit_atm') }}</a></li>
                    <li><a href="{{ route('profile.deposit-usdt') }}"><span class="iconify"
                                data-icon="ant-design:bank-outlined"></span> {{ __('shop.nav_deposit_usdt') }}</a></li>
                </ul>
            </li>

            <!-- History & News Links -->
            <li><a href="{{ route('profile.transaction-history') }}" class="nav-link-item"><span class="iconify"
                        data-icon="ant-design:history-outlined"></span> {{ __('shop.nav_history') }}</a></li>
            <li><a href="{{ route('news.index') }}" class="nav-link-item"><span class="iconify"
                        data-icon="ant-design:notification-outlined"></span> {{ __('shop.nav_news') }}</a></li>

            <!-- Affiliate Link (Harmonized Color) -->
            <li><a href="{{ route('profile.affiliate') }}" class="nav-link-item"><span class="iconify"
                        data-icon="ant-design:link-outlined"></span> {{ __('shop.nav_affiliate') }}</a></li>

            @if(Auth::check() && Auth::user()->role === 'admin')
            <li class="mobile-only-admin">
                <a href="{{ route('admin.index') }}" target="_blank" class="nav-link-item" style="color: #fbbf24 !important; font-weight: 700;">
                    <span class="iconify" data-icon="ant-design:dashboard-outlined"></span> Admin Panel
                </a>
            </li>
            @endif
        </ul>

        <!-- Right Side: User Controls & Actions -->
        <div class="nav-user">
            @if(Auth::check() && Auth::user()->role === 'admin')
            <a href="{{ route('admin.index') }}" target="_blank" class="btn-admin-panel" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.45); color: #fbbf24; font-weight: 700; font-size: 0.8rem; text-decoration: none; transition: all 0.2s; white-space: nowrap; flex-shrink: 0;">
                <span class="iconify" data-icon="ant-design:dashboard-outlined" style="font-size: 14px;"></span> Admin Panel
            </a>
            @endif

            <!-- Language Switcher Dropdown (VI / EN) with Dark Slate Glass -->
            <div class="ant-header-lang-dropdown mr-2" style="position: relative; flex-shrink: 0;">
                <div class="ant-header-lang-trigger" id="langTrigger">
                    <img src="{{ $isEn ? 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.4.3/flags/4x3/us.svg' : 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.4.3/flags/4x3/vn.svg' }}" 
                         id="currentLangFlag" alt="Flag" style="width: 18px; height: 13px; border-radius: 2px; object-fit: cover;">
                    <span class="ant-header-lang-text" id="currentLangText">{{ $isEn ? 'EN' : 'VI' }}</span>
                    <span class="iconify" data-icon="ant-design:down-outlined" style="font-size: 10px; color: #94a3b8;"></span>
                </div>
                <div class="ant-dropdown-menu" id="langDropdownMenu">
                    <div class="ant-dropdown-item {{ !$isEn ? 'active' : '' }}" onclick="setLanguage('vi')">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.4.3/flags/4x3/vn.svg" alt="VN" style="width: 18px; height: 13px; border-radius: 2px; object-fit: cover;">
                        <span style="font-weight: 600; font-size: 0.84rem;">{{ __('shop.lang_vi') }}</span>
                    </div>
                    <div class="ant-dropdown-item {{ $isEn ? 'active' : '' }}" onclick="setLanguage('en')">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.4.3/flags/4x3/us.svg" alt="EN" style="width: 18px; height: 13px; border-radius: 2px; object-fit: cover;">
                        <span style="font-weight: 600; font-size: 0.84rem;">{{ __('shop.lang_en') }}</span>
                    </div>
                </div>
            </div>

            <!-- Deposit Button -->
            <a href="javascript:void(0)" onclick="document.getElementById('depositMethodModal').style.display='flex'" class="btn-deposit-glow" style="background: linear-gradient(135deg, #E8B33C, #C2922A); color: #fff; padding: 7px 14px; border-radius: 20px; font-weight: 700; font-size: 0.82rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 14px rgba(232, 179, 60, 0.25); white-space: nowrap; flex-shrink: 0;">
                <span class="iconify" data-icon="ant-design:plus-circle-outlined"></span> {{ __('shop.nav_deposit') }}
            </a>

            <!-- Auth / Login State -->
            @if(Auth::check())
            <div class="nav-avatar-wrapper" id="avatarWrapper" style="position: relative; flex-shrink: 0;">
                <div class="nav-user-profile" onclick="toggleAvatarMenu()"
                    style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <div style="text-align:right;">
                        <div style="font-weight:600;font-size:0.85rem;line-height:1.2;color:#f8fafc;">{{ Auth::user()->username }}</div>
                        <div style="font-size:0.75rem;color:var(--yuki-gold, #E8B33C);">{{ number_format(Auth::user()->balance) }}đ</div>
                    </div>
                    <button class="nav-avatar" id="avatarBtn" aria-label="User menu" style="background: linear-gradient(135deg, #E8B33C, #C2922A); color: #fff; border: 1px solid rgba(255,255,255,0.2); width:34px; height:34px; border-radius:50%; font-weight:700; cursor:pointer;">
                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                    </button>
                </div>
                <div class="avatar-dropdown" id="avatarDropdown" style="display:none; position:absolute; right:0; top:calc(100% + 8px); width:220px; background: #0d1322; border: 1px solid rgba(255,255,255,0.12); border-radius:12px; box-shadow: 0 16px 36px rgba(0,0,0,0.7); z-index:10000; padding:8px 0;">
                    <div class="dropdown-header" style="padding: 10px 16px;">
                        <div class="dropdown-name" style="color: #f8fafc; font-weight: 700;">{{ Auth::user()->username }}</div>
                        <div class="dropdown-email" style="color: #94a3b8; font-size: 0.8rem;">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                    <div class="dropdown-divider" style="border-top: 1px solid rgba(255,255,255,0.08); margin: 6px 0;"></div>
                    <a href="/profile" class="dropdown-item" style="display:flex; align-items:center; gap:8px; padding:8px 16px; color: #cbd5e1; text-decoration:none; font-size:0.85rem;">
                        <span class="iconify" data-icon="ant-design:dashboard-outlined"></span> {{ __('shop.nav_profile') }}
                    </a>
                    <a href="{{ route('profile.deposit-card') }}" class="dropdown-item" style="display:flex; align-items:center; gap:8px; padding:8px 16px; color: #cbd5e1; text-decoration:none; font-size:0.85rem;">
                        <span class="iconify" data-icon="ant-design:dollar-outlined"></span> {{ __('shop.nav_deposit') }}
                    </a>
                    <a href="{{ route('profile.transaction-history') }}" class="dropdown-item" style="display:flex; align-items:center; gap:8px; padding:8px 16px; color: #cbd5e1; text-decoration:none; font-size:0.85rem;">
                        <span class="iconify" data-icon="ant-design:history-outlined"></span> {{ __('shop.nav_history') }}
                    </a>
                    @if (Auth::check() && Auth()->user()->role == 'admin')
                    <a href="{{ route('admin.index') }}" target="_blank" class="dropdown-item" style="display:flex; align-items:center; gap:8px; padding:10px 16px; color: #fbbf24; text-decoration:none; font-size:0.85rem; font-weight: 700; background: rgba(245, 158, 11, 0.1); border-radius: 6px; margin: 4px 8px;">
                        <span class="iconify" data-icon="ant-design:dashboard-outlined" style="font-size: 16px;"></span> {{ __('shop.nav_admin') }} <span style="font-size: 10px; background: #fbbf24; color: #0b0f19; padding: 1px 6px; border-radius: 4px; margin-left: auto; font-weight: 800;">ADMIN</span>
                    </a>
                    @endif
                    <div class="dropdown-divider" style="border-top: 1px solid rgba(255,255,255,0.08); margin: 6px 0;"></div>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;width:100%;">
                        @csrf
                        <button type="submit" class="dropdown-item dropdown-logout" style="width:100%; text-align:left; background:transparent; border:none; cursor:pointer; color:#ef4444; display:flex; align-items:center; gap:8px; padding:8px 16px; font-size:0.85rem;">
                            <span class="iconify" data-icon="ant-design:logout-outlined"></span> {{ __('shop.nav_logout') }}
                        </button>
                    </form>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}" style="text-decoration:none;font-weight:700;padding:7px 16px;border-radius:20px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);color:#fff;font-size:0.85rem;display:inline-flex;align-items:center;gap:6px;transition:all 0.2s;">
                <span class="iconify" data-icon="ant-design:user-outlined"></span> {{ __('shop.nav_login') }}
            </a>
            @endif
        </div>
    </div>
</nav>
<div class="nav-overlay" id="navOverlay" onclick="closeNav()"></div>

<script>
    // Master-Detail Category Tab Switcher
    function switchNavCatTab(tabEl, panelId) {
        if (!tabEl) return;
        var tabs = document.querySelectorAll('.cat-tab-item');
        tabs.forEach(function(el) { el.classList.remove('active'); });
        tabEl.classList.add('active');

        var panels = document.querySelectorAll('.cat-detail-panel');
        panels.forEach(function(panel) { panel.classList.remove('active'); });

        var target = document.getElementById(panelId);
        if (target) {
            target.classList.add('active');
        }
    }

    // Toggle Dropdown On Click & Close on Outside Click
    document.addEventListener('DOMContentLoaded', function() {
        var catDropdown = document.getElementById('navCategoriesDropdown');
        var catTrigger = document.getElementById('navCategoriesTrigger');

        if (catTrigger && catDropdown) {
            catTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                catDropdown.classList.toggle('open');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (catDropdown && !catDropdown.contains(e.target)) {
                catDropdown.classList.remove('open');
            }
            var avatarDropdown = document.getElementById('avatarDropdown');
            var avatarWrapper = document.getElementById('avatarWrapper');
            if (avatarDropdown && avatarWrapper && !avatarWrapper.contains(e.target)) {
                avatarDropdown.style.display = 'none';
            }
        });

        // Close on Esc key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (catDropdown) catDropdown.classList.remove('open');
                var avatarDropdown = document.getElementById('avatarDropdown');
                if (avatarDropdown) avatarDropdown.style.display = 'none';
            }
        });
    });

    // Mobile Navigation Controls
    function toggleNav() {
        var nav = document.getElementById('navLinks');
        var overlay = document.getElementById('navOverlay');
        if (nav) nav.classList.toggle('show');
        if (overlay) overlay.classList.toggle('show');
    }
    function closeNav() {
        var nav = document.getElementById('navLinks');
        var overlay = document.getElementById('navOverlay');
        if (nav) nav.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
    }

    // Avatar Menu Toggle
    function toggleAvatarMenu() {
        var dropdown = document.getElementById('avatarDropdown');
        if (dropdown) {
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }
    }
</script>
