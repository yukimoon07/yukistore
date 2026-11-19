<?php $__env->startSection('title', $category->name); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero Section -->
    <?php if (isset($component)) { $__componentOriginal676d920e8bb32a4c96cd6e6c6ba00de0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal676d920e8bb32a4c96cd6e6c6ba00de0 = $attributes; } ?>
<?php $component = App\View\Components\HeroHeader::resolve(['title' => ''.e($category->name).'','description' => ''.e($category->description).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('hero-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\HeroHeader::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal676d920e8bb32a4c96cd6e6c6ba00de0)): ?>
<?php $attributes = $__attributesOriginal676d920e8bb32a4c96cd6e6c6ba00de0; ?>
<?php unset($__attributesOriginal676d920e8bb32a4c96cd6e6c6ba00de0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal676d920e8bb32a4c96cd6e6c6ba00de0)): ?>
<?php $component = $__componentOriginal676d920e8bb32a4c96cd6e6c6ba00de0; ?>
<?php unset($__componentOriginal676d920e8bb32a4c96cd6e6c6ba00de0); ?>
<?php endif; ?>

    <!-- Account List Section -->
    <section class="account-section" style="padding: 32px 0 60px;">
        <div class="container">
            <!-- Filter Bar -->
            <form action="" method="GET" class="filter-inline-bar" style="background: var(--bg-card, #151f32); border: 1px solid var(--border-card, #1e2c45); border-radius: 14px; padding: 16px 20px; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 16px rgba(0,0,0,0.25);">
                <input type="text" name="code" class="filter-input" placeholder="<?php echo e(__('shop.filter_code')); ?>" value="<?php echo e(request('code')); ?>" style="background: var(--bg-input, #0e1627); border: 1px solid var(--border-subtle, rgba(255,255,255,0.08)); border-radius: 8px; color: #fff; padding: 8px 14px; font-size: 0.88rem; min-width: 130px; flex: 1;">
                
                <input type="number" name="price_from" class="filter-input" placeholder="<?php echo e(__('shop.filter_price_from')); ?>" value="<?php echo e(request('price_from')); ?>" style="background: var(--bg-input, #0e1627); border: 1px solid var(--border-subtle, rgba(255,255,255,0.08)); border-radius: 8px; color: #fff; padding: 8px 14px; font-size: 0.88rem; min-width: 110px; flex: 1;">
                <input type="number" name="price_to" class="filter-input" placeholder="<?php echo e(__('shop.filter_price_to')); ?>" value="<?php echo e(request('price_to')); ?>" style="background: var(--bg-input, #0e1627); border: 1px solid var(--border-subtle, rgba(255,255,255,0.08)); border-radius: 8px; color: #fff; padding: 8px 14px; font-size: 0.88rem; min-width: 110px; flex: 1;">

                <?php $__currentLoopData = $dynamicKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <input type="text" name="details[<?php echo e($key); ?>]" class="filter-input" placeholder="<?php echo e($key); ?>..." value="<?php echo e(request("details.{$key}")); ?>" style="background: var(--bg-input, #0e1627); border: 1px solid var(--border-subtle, rgba(255,255,255,0.08)); border-radius: 8px; color: #fff; padding: 8px 14px; font-size: 0.88rem; min-width: 130px; flex: 1;">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <select name="status" class="filter-select" style="background: var(--bg-input, #0e1627); border: 1px solid var(--border-subtle, rgba(255,255,255,0.08)); border-radius: 8px; color: #fff; padding: 8px 14px; font-size: 0.88rem; min-width: 130px;">
                    <option value="" style="background: #111827;"><?php echo e(__('shop.filter_status_all')); ?></option>
                    <option value="available" <?php echo e(request('status') == 'available' ? 'selected' : ''); ?> style="background: #111827;"><?php echo e(__('shop.filter_status_available')); ?></option>
                    <option value="sold" <?php echo e(request('status') == 'sold' ? 'selected' : ''); ?> style="background: #111827;"><?php echo e(__('shop.filter_status_sold')); ?></option>
                </select>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="filter-btn filter-btn-search" style="background: linear-gradient(135deg, #2563eb, #06b6d4); color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-search"></i> <?php echo e(__('shop.filter_search_btn')); ?>

                    </button>
                    <a href="<?php echo e(request()->url()); ?>" class="filter-btn filter-btn-reset" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 14px; font-weight: 600; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-rotate-right"></i> <?php echo e(__('shop.filter_reset_btn')); ?>

                    </a>
                </div>
            </form>

            <!-- Account Grid -->
            <div class="account-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
                <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="account-card" style="background: var(--bg-card, #151f32); border: 1px solid var(--border-card, #1e2c45); border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s; box-shadow: 0 4px 16px rgba(0,0,0,0.3);">
                        <div class="account-media" style="position: relative; width: 100%; aspect-ratio: 16/10; overflow: hidden; background: #0b0f19;">
                            <a href="<?php echo e(route('account.show', ['id' => $account->id])); ?>">
                                <img src="<?php echo e($account->thumb); ?>" alt="Account Preview" class="account-img" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                            </a>
                            <div class="account-code" style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); color: #fff; font-size: 0.78rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
                                <?php echo e(__('shop.detail_id')); ?>: #<?php echo e($account->id); ?>

                            </div>
                            <div class="account-price-top" style="position: absolute; top: 8px; right: 8px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(4px); color: var(--accent-amber, #f59e0b); font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
                                <?php echo e(number_format($account->price)); ?> đ
                            </div>
                        </div>

                        <div class="account-info" style="padding: 14px; flex-grow: 1;">
                            <?php
                                $details = is_array($account->details) ? $account->details : json_decode($account->details, true) ?? [];
                            ?>
                            
                            <?php if(count($details) > 0): ?>
                                <div class="account-row" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px;">
                                    <?php $__currentLoopData = array_slice($details, 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="info-item" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px; padding: 4px 8px; font-size: 0.78rem; flex: 1 1 45%; display: flex; justify-content: space-between; overflow: hidden;">
                                        <span class="info-item__title" style="color: #94a3b8; font-weight: 500;"><?php echo e($detail['key'] ?? ''); ?>:</span>
                                        <span class="info-value" style="color: #f8fafc; font-weight: 700; margin-left: 4px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"><?php echo e($detail['value'] ?? ''); ?></span>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="account-actions" style="padding: 12px 14px; border-top: 1px solid var(--border-subtle, rgba(255,255,255,0.06)); display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                            <div class="card-price" style="font-size: 0.76rem; color: #94a3b8; font-weight: 600;">
                                <?php echo e(__('shop.detail_price_card')); ?>: <span style="color: #cbd5e1;"><?php echo e(number_format($account->price / ((100 - config_get('payment.card.discount_percent')) / 100))); ?> đ</span>
                            </div>
                            <a href="<?php echo e(route('account.show', ['id' => $account->id])); ?>"
                                class="action-btn action-btn--detail" style="background: linear-gradient(135deg, #2563eb, #06b6d4); color: #fff; padding: 7px 14px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; text-decoration: none; text-transform: uppercase; letter-spacing: 0.3px; transition: all 0.2s; white-space: nowrap;">
                                <?php echo e(__('shop.btn_view_detail')); ?>

                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="no-data-empty" style="text-align: center; padding: 80px 20px; grid-column: 1 / -1; width: 100%; background: var(--bg-card, #151f32); border-radius: 16px; border: 1px solid var(--border-card, #1e2c45);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 56px; height: 56px; color: #64748b; margin-bottom: 16px;">
                            <path d="M21 8v13H3V8"></path>
                            <path d="M1 3h22v5H1z"></path>
                            <path d="M10 12h4v4h-4z"></path>
                        </svg>
                        <p style="color: #94a3b8; font-size: 1.05rem; font-weight: 600; margin: 0;"><?php echo e(__('shop.empty_accounts')); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <div style="display: flex; justify-content: center; width: 100%; margin-top: 36px;">
                <?php echo e($accounts->links('user.pagination.custom')); ?>

            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Le Dat\Downloads\shopgame\resources\views/user/category/show.blade.php ENDPATH**/ ?>