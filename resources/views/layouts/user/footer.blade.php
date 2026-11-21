<footer class="footer" style="background:#080c14;border-top:1px solid rgba(255,255,255,0.08);color:#94a3b8;margin-top:auto;padding-top:48px;">
    <div class="container">
        <div class="footer-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:40px;margin-bottom:40px;">
            <!-- Brand -->
            <div class="footer-brand">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <img src="{{ config_get('site_logo_footer', config_get('site_logo')) ?: '/img/logo.svg' }}" alt="{{ config_get('site_name', 'ShopGame') }}" width="120" height="32" style="height:32px; width:auto; object-fit:contain;" loading="lazy" onerror="this.src='/img/logo.svg'">
                </div>
                <p style="font-size:0.85rem;line-height:1.7;margin:0;color:#94a3b8;">
                    {{ config_get('site_description') }}
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 style="color:#f8fafc;font-size:0.9rem;font-weight:700;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;">{{ __('shop.footer_quick_links') }}</h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                    <li><a href="/" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;">{{ __('shop.nav_home') }}</a></li>
                    <li><a href="{{ route('service.show-all') }}" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;">{{ __('shop.nav_services') }}</a></li>
                    <li><a href="{{ route('profile.deposit-card') }}" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;">{{ __('shop.nav_deposit') }}</a></li>
                    <li><a href="{{ route('profile.transaction-history') }}" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;">{{ __('shop.nav_history') }}</a></li>
                    <li><a href="{{ route('news.index') }}" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;">{{ __('shop.nav_news') }}</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 style="color:#f8fafc;font-size:0.9rem;font-weight:700;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;">{{ __('shop.footer_support') }}</h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                    <li><a href="{{ route('faq') }}" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;">{{ __('shop.footer_faq') }}</a></li>
                    <li><a href="{{ route('terms') }}" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;">{{ __('shop.footer_terms') }}</a></li>
                    <li><a href="{{ route('privacy') }}" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;">{{ __('shop.footer_privacy') }}</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 style="color:#f8fafc;font-size:0.9rem;font-weight:700;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;">{{ __('shop.footer_contact') }}</h4>
                <div style="display:flex;flex-direction:column;gap:10px;font-size:0.85rem;color:#cbd5e1;">
                    <div><i class="fa-solid fa-phone me-2" style="color:var(--yuki-gold, #E8B33C);"></i> Zalo: <a href="https://zalo.me/0373992680" target="_blank" rel="noopener noreferrer" style="color:#f8fafc;font-weight:600;text-decoration:none;">0373992680</a></div>
                    <div><i class="fa-brands fa-facebook me-2" style="color:#3b82f6;"></i> Facebook: <a href="https://www.facebook.com/profile.php?id=61554102541296" target="_blank" rel="noopener noreferrer" style="color:var(--yuki-gold, #E8B33C);text-decoration:none;">Yukistore Official</a></div>
                    <div><i class="fa-brands fa-discord me-2" style="color:#6366f1;"></i> Discord: <a href="https://discord.gg/sPPEKffZrH" target="_blank" rel="noopener noreferrer" style="color:var(--yuki-gold, #E8B33C);text-decoration:none;">Tham gia Discord</a></div>
                    @if(config_get('email'))
                    <div><i class="fa-solid fa-envelope me-2" style="color:#94a3b8;"></i> Email: <a href="mailto:{{ config_get('email') }}" style="color:var(--yuki-gold, #E8B33C);text-decoration:none;">{{ config_get('email') }}</a></div>
                    @endif
                    <div><i class="fa-solid fa-clock me-2" style="color:#10b981;"></i> {{ __('shop.working_hours') }}: 24/7 Tự động</div>
                </div>
                <div style="display:flex;gap:12px;margin-top:16px;">
                    <a href="https://zalo.me/0373992680" target="_blank" rel="noopener noreferrer" aria-label="Zalo" style="color:#38bdf8;font-size:1.4rem;transition:transform .2s;" title="Zalo: 0373992680"><i class="fa-solid fa-comment-dots"></i></a>
                    <a href="https://www.facebook.com/profile.php?id=61554102541296" target="_blank" rel="noopener noreferrer" aria-label="Facebook" style="color:#3b82f6;font-size:1.4rem;transition:transform .2s;" title="Facebook Fanpage"><i class="fab fa-facebook"></i></a>
                    <a href="https://discord.gg/sPPEKffZrH" target="_blank" rel="noopener noreferrer" aria-label="Discord" style="color:#818cf8;font-size:1.4rem;transition:transform .2s;" title="Discord Community"><i class="fab fa-discord"></i></a>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div style="border-top:1px solid rgba(255,255,255,0.06);padding:20px 0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;font-size:0.82rem;color:#64748b;">
            <div>Bản quyền web thuộc Yukistore. © {{ date('Y') }} Yukistore. All rights reserved.</div>
            <div>Uy tín - Chất lượng - Nhanh chóng 24/7</div>
        </div>
    </div>
</footer>

<!-- Mobile Bottom Nav -->
<nav class="mobile-bottom-nav" style="background:rgba(11,15,25,0.95);border-top:1px solid rgba(255,255,255,0.08);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);">
    <a href="/" class="bottom-nav-item {{ request()->is('/') ? 'active' : '' }}">
        <span class="iconify" data-icon="ant-design:home-outlined"></span>
        <span>{{ __('shop.nav_home') }}</span>
    </a>
    <a href="/#categories" class="bottom-nav-item">
        <span class="iconify" data-icon="ant-design:appstore-outlined"></span>
        <span>{{ __('shop.nav_categories') }}</span>
    </a>
    <a href="javascript:void(0)" class="bottom-nav-item" onclick="document.getElementById('depositMethodModal').style.display='flex'">
        <span class="iconify" data-icon="ant-design:wallet-outlined"></span>
        <span>{{ __('shop.nav_deposit') }}</span>
    </a>
    <a href="{{ route('profile.transaction-history') }}" class="bottom-nav-item {{ request()->routeIs('profile.transaction*') ? 'active' : '' }}">
        <span class="iconify" data-icon="ant-design:history-outlined"></span>
        <span>{{ __('shop.nav_history') }}</span>
    </a>
    @if(Auth::check())
    <a href="/profile" class="bottom-nav-item {{ request()->is('profile*') ? 'active' : '' }}">
        <span class="iconify" data-icon="ant-design:user-outlined"></span>
        <span>{{ __('shop.nav_profile') }}</span>
    </a>
    @else
    <a href="{{ route('login') }}" class="bottom-nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
        <span class="iconify" data-icon="ant-design:login-outlined"></span>
        <span>{{ __('shop.nav_login') }}</span>
    </a>
    @endif
</nav>
