<footer class="footer" style="background:#080c14;border-top:1px solid rgba(255,255,255,0.08);color:#94a3b8;margin-top:auto;padding-top:48px;">
    <div class="container">
        <div class="footer-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:40px;margin-bottom:40px;">
            <!-- Brand -->
            <div class="footer-brand">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <img src="{{ config_get('site_logo_footer', config_get('site_logo')) }}" alt="{{ config_get('site_name') }}" width="120" height="32" style="height:32px; width:auto; object-fit:contain;" loading="lazy">
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
                    <li><a href="{{ route('missions.index') }}" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;">{{ __('shop.nav_missions') }}</a></li>
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
                    @if(config_get('email'))
                    <div>Email: <a href="mailto:{{ config_get('email') }}" style="color:var(--yuki-gold, #E8B33C);text-decoration:none;">{{ config_get('email') }}</a></div>
                    @endif
                    @if(config_get('phone'))
                    <div>Hotline: <span style="color:#f8fafc;font-weight:600;">{{ config_get('phone') }}</span></div>
                    @endif
                    @if(config_get('address'))
                    <div>{{ config_get('address') }}</div>
                    @endif
                    @if(config_get('working_hours'))
                    <div>{{ __('shop.working_hours') }}: {{ config_get('working_hours') }}</div>
                    @endif
                </div>
                <div style="display:flex;gap:12px;margin-top:16px;">
                    @if(config_get('facebook'))
                    <a href="{{ config_get('facebook') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" style="color:var(--yuki-gold, #E8B33C);font-size:1.4rem;transition:transform .2s;"><i class="fab fa-facebook"></i></a>
                    @endif
                    @if(config_get('youtube'))
                    <a href="{{ config_get('youtube') }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" style="color:#ef4444;font-size:1.4rem;transition:transform .2s;"><i class="fab fa-youtube"></i></a>
                    @endif
                    @if(config_get('telegram'))
                    <a href="{{ config_get('telegram') }}" target="_blank" rel="noopener noreferrer" aria-label="Telegram" style="color:#C2922A;font-size:1.4rem;transition:transform .2s;"><i class="fab fa-telegram"></i></a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div style="border-top:1px solid rgba(255,255,255,0.06);padding:20px 0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;font-size:0.82rem;color:#64748b;">
            <div>© {{ date('Y') }} {{ config_get('site_name', 'ShopGame') }}. {{ __('shop.footer_rights') }}</div>
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
