<footer class="footer" style="background:#080c14;border-top:1px solid rgba(255,255,255,0.08);color:#94a3b8;margin-top:auto;padding-top:48px;">
    <div class="container">
        <div class="footer-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:40px;margin-bottom:40px;">
            <!-- Brand -->
            <div class="footer-brand">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <img src="<?php echo e(config_get('site_logo_footer', config_get('site_logo'))); ?>" alt="<?php echo e(config_get('site_name')); ?>" width="120" height="32" style="height:32px; width:auto; object-fit:contain;" loading="lazy">
                </div>
                <p style="font-size:0.85rem;line-height:1.7;margin:0;color:#94a3b8;">
                    <?php echo e(config_get('site_description')); ?>

                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 style="color:#f8fafc;font-size:0.9rem;font-weight:700;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;"><?php echo e(__('shop.footer_quick_links')); ?></h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                    <li><a href="/" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;"><?php echo e(__('shop.nav_home')); ?></a></li>
                    <li><a href="<?php echo e(route('service.show-all')); ?>" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;"><?php echo e(__('shop.nav_services')); ?></a></li>
                    <li><a href="<?php echo e(route('missions.index')); ?>" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;"><?php echo e(__('shop.nav_missions')); ?></a></li>
                    <li><a href="<?php echo e(route('profile.deposit-card')); ?>" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;"><?php echo e(__('shop.nav_deposit')); ?></a></li>
                    <li><a href="<?php echo e(route('profile.transaction-history')); ?>" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;"><?php echo e(__('shop.nav_history')); ?></a></li>
                    <li><a href="<?php echo e(route('news.index')); ?>" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;"><?php echo e(__('shop.nav_news')); ?></a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 style="color:#f8fafc;font-size:0.9rem;font-weight:700;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;"><?php echo e(__('shop.footer_support')); ?></h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                    <li><a href="<?php echo e(route('faq')); ?>" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;"><?php echo e(__('shop.footer_faq')); ?></a></li>
                    <li><a href="<?php echo e(route('terms')); ?>" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;"><?php echo e(__('shop.footer_terms')); ?></a></li>
                    <li><a href="<?php echo e(route('privacy')); ?>" class="footer-link" style="color:#cbd5e1;text-decoration:none;font-size:0.85rem;transition:color .2s;"><?php echo e(__('shop.footer_privacy')); ?></a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 style="color:#f8fafc;font-size:0.9rem;font-weight:700;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;"><?php echo e(__('shop.footer_contact')); ?></h4>
                <div style="display:flex;flex-direction:column;gap:10px;font-size:0.85rem;color:#cbd5e1;">
                    <?php if(config_get('email')): ?>
                    <div>Email: <a href="mailto:<?php echo e(config_get('email')); ?>" style="color:#38bdf8;text-decoration:none;"><?php echo e(config_get('email')); ?></a></div>
                    <?php endif; ?>
                    <?php if(config_get('phone')): ?>
                    <div>Hotline: <span style="color:#f8fafc;font-weight:600;"><?php echo e(config_get('phone')); ?></span></div>
                    <?php endif; ?>
                    <?php if(config_get('address')): ?>
                    <div><?php echo e(config_get('address')); ?></div>
                    <?php endif; ?>
                    <?php if(config_get('working_hours')): ?>
                    <div><?php echo e(__('shop.working_hours')); ?>: <?php echo e(config_get('working_hours')); ?></div>
                    <?php endif; ?>
                </div>
                <div style="display:flex;gap:12px;margin-top:16px;">
                    <?php if(config_get('facebook')): ?>
                    <a href="<?php echo e(config_get('facebook')); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" style="color:#38bdf8;font-size:1.4rem;transition:transform .2s;"><i class="fab fa-facebook"></i></a>
                    <?php endif; ?>
                    <?php if(config_get('youtube')): ?>
                    <a href="<?php echo e(config_get('youtube')); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube" style="color:#ef4444;font-size:1.4rem;transition:transform .2s;"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if(config_get('telegram')): ?>
                    <a href="<?php echo e(config_get('telegram')); ?>" target="_blank" rel="noopener noreferrer" aria-label="Telegram" style="color:#06b6d4;font-size:1.4rem;transition:transform .2s;"><i class="fab fa-telegram"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div style="border-top:1px solid rgba(255,255,255,0.06);padding:20px 0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;font-size:0.82rem;color:#64748b;">
            <div>© <?php echo e(date('Y')); ?> <?php echo e(config_get('site_name', 'ShopGame')); ?>. <?php echo e(__('shop.footer_rights')); ?></div>
        </div>
    </div>
</footer>

<!-- Mobile Bottom Nav -->
<nav class="mobile-bottom-nav" style="background:rgba(11,15,25,0.95);border-top:1px solid rgba(255,255,255,0.08);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);">
    <a href="/" class="bottom-nav-item <?php echo e(request()->is('/') ? 'active' : ''); ?>">
        <span class="iconify" data-icon="ant-design:home-outlined"></span>
        <span><?php echo e(__('shop.nav_home')); ?></span>
    </a>
    <a href="/#categories" class="bottom-nav-item">
        <span class="iconify" data-icon="ant-design:appstore-outlined"></span>
        <span><?php echo e(__('shop.nav_categories')); ?></span>
    </a>
    <a href="javascript:void(0)" class="bottom-nav-item" onclick="document.getElementById('depositMethodModal').style.display='flex'">
        <span class="iconify" data-icon="ant-design:wallet-outlined"></span>
        <span><?php echo e(__('shop.nav_deposit')); ?></span>
    </a>
    <a href="<?php echo e(route('profile.transaction-history')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs('profile.transaction*') ? 'active' : ''); ?>">
        <span class="iconify" data-icon="ant-design:history-outlined"></span>
        <span><?php echo e(__('shop.nav_history')); ?></span>
    </a>
    <?php if(Auth::check()): ?>
    <a href="/profile" class="bottom-nav-item <?php echo e(request()->is('profile*') ? 'active' : ''); ?>">
        <span class="iconify" data-icon="ant-design:user-outlined"></span>
        <span><?php echo e(__('shop.nav_profile')); ?></span>
    </a>
    <?php else: ?>
    <a href="<?php echo e(route('login')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs('login') ? 'active' : ''); ?>">
        <span class="iconify" data-icon="ant-design:login-outlined"></span>
        <span><?php echo e(__('shop.nav_login')); ?></span>
    </a>
    <?php endif; ?>
</nav>
<?php /**PATH C:\Users\Le Dat\Downloads\shopgame\resources\views/layouts/user/footer.blade.php ENDPATH**/ ?>