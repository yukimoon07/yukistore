@extends('layouts.user.app')

@section('title', $title . ' - Dịch Vụ Cày Thuê Uy Tín')

@section('content')
    <!-- SEO JSON-LD Structured Data: BreadcrumbList -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Trang Chủ",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Dịch Vụ Game",
                "item": "{{ route('service.show-all') }}"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ $service->name }}",
                "item": "{{ url()->current() }}"
            }
        ]
    }
    </script>

    <!-- SEO JSON-LD Structured Data: Service & Product Schema -->
    @php
        $validPackages = $service->activePackages;
        $hasValidPrice = $validPackages->count() > 0;
    @endphp
    @if($hasValidPrice)
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "serviceType": "Dịch Vụ Game",
        "name": "{{ $service->name }}",
        "description": "{{ $service->description ?? 'Dịch vụ cày thuê game uy tín, bảo mật 100%.' }}",
        "provider": {
            "@type": "Organization",
            "name": "{{ config_get('site_name', 'ShopGame') }}",
            "url": "{{ url('/') }}"
        },
        "offers": {
            "@type": "AggregateOffer",
            "priceCurrency": "VND",
            "lowPrice": "{{ $validPackages->min('price') }}",
            "highPrice": "{{ $validPackages->max('price') }}",
            "offerCount": "{{ $validPackages->count() }}"
        }
    }
    </script>
    @endif

    <div class="service-detail-wrapper">
        <div class="container">
            <!-- Breadcrumb Navigation -->
            <nav class="gaming-breadcrumb" aria-label="Breadcrumb">
                <a href="/"><i class="fa-solid fa-house"></i> {{ __('shop.nav_home') }}</a>
                <span class="gaming-breadcrumb-sep">/</span>
                <a href="{{ route('service.show-all') }}">{{ __('shop.nav_services') }}</a>
                <span class="gaming-breadcrumb-sep">/</span>
                <span class="gaming-breadcrumb-current">{{ $service->name }}</span>
            </nav>

            <!-- Alerts -->
            @if (session('error'))
                <div class="warning-alert-box" style="margin-bottom: 16px;">
                    <i class="fa-solid fa-circle-exclamation warning-alert-icon"></i>
                    <div class="security-alert-content">
                        <strong>Lỗi:</strong> {{ session('error') }}
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="security-alert-box" style="margin-bottom: 16px;">
                    <i class="fa-solid fa-circle-check security-alert-icon"></i>
                    <div class="security-alert-content">
                        <strong>Thành công:</strong> {{ session('success') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="warning-alert-box" style="margin-bottom: 16px;">
                    <i class="fa-solid fa-circle-exclamation warning-alert-icon"></i>
                    <div class="security-alert-content">
                        <strong>Vui lòng kiểm tra lại:</strong>
                        <ul style="margin: 4px 0 0 16px; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Service Header Banner with Stepper (Image 4) -->
            <header class="service-header-banner">
                <div class="service-header-left">
                    <div class="service-security-pill">
                        <i class="fa-solid fa-shield-halved"></i> Dịch Vụ Bảo Mật - Xử Lý Thủ Công
                    </div>
                    <h1 class="service-header-title">{{ $service->name }}</h1>
                    <p class="service-header-desc">
                        {{ $service->description ?: 'Chọn đúng gói dịch vụ cần thuê, điền thông tin tài khoản và theo dõi tiến độ đơn hàng trực tiếp trong hệ thống.' }}
                    </p>
                </div>

                <!-- Steps Indicator for Checkout (Material UI / Ant Design Steps) -->
                <div style="flex: 1; max-width: 580px; min-width: 300px;">
                    <x-checkout-stepper 
                        id="serviceCheckoutStepper"
                        :activeStep="2"
                        :steps="[
                            ['title' => 'Chọn gói', 'desc' => 'Dịch vụ'],
                            ['title' => 'Thông tin', 'desc' => 'Tài khoản'],
                            ['title' => 'Thanh toán', 'desc' => 'Payment'],
                            ['title' => 'Hoàn tất', 'desc' => 'Bàn giao'],
                        ]"
                    />
                </div>
            </header>

            <!-- Trust & Safety Notices (Image 4) -->
            @if(isset($activeOrder) && $activeOrder)
                <div class="warning-alert-box" style="margin-bottom: 20px; border-color: #ef4444; background: rgba(239, 68, 68, 0.12); padding: 18px 20px; border-radius: 14px;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #ef4444; font-size: 2rem; flex-shrink: 0; margin-top: 2px;"></i>
                    <div class="security-alert-content">
                        <strong style="color: #ef4444; font-size: 1.05rem;">BẠN ĐANG CÓ ĐƠN CÀY THUÊ ĐANG XỬ LÝ!</strong>
                        <p style="margin: 6px 0 0; color: #f8fafc; font-size: 0.92rem; line-height: 1.5;">
                            Đơn hàng <strong>#{{ $activeOrder->id }}</strong> (Trạng thái: <strong>{{ $activeOrder->status === 'pending' ? 'Đang chờ nhận đơn' : 'Đang tiến hành cày' }}</strong>) đang được kỹ thuật viên xử lý. Theo quy định, mỗi khách hàng chỉ được đặt <strong>1 đơn trong lúc cày</strong>. Sau khi hoàn tất bạn mới có thể đặt tiếp đơn mới.
                        </p>
                    </div>
                </div>
            @endif

            <div class="security-alert-box">
                <i class="fa-solid fa-lock security-alert-icon"></i>
                <div class="security-alert-content">
                    <strong>Thông tin đăng nhập được mã hóa an toàn:</strong> Chỉ Admin và CTV cày thuê được phân quyền xử lý mới xem được thông tin. Mật khẩu và mã bảo mật 2FA sẽ tự động xóa ngay sau khi đơn hàng được hoàn tất hoặc từ chối.
                </div>
            </div>

            <div class="warning-alert-box">
                <i class="fa-solid fa-triangle-exclamation warning-alert-icon"></i>
                <div class="security-alert-content">
                    <strong>Lưu ý trong khi đơn đang cày:</strong> Vui lòng không tự ý đăng nhập vào tài khoản game trong suốt quá trình cày thuê để tránh làm ngắt kết nối nhân viên và đảm bảo hoàn thành đúng thời hạn.
                </div>
            </div>

            <!-- Main 2-Column Split Workspace (Image 4) -->
            <div class="service-checkout-grid">
                
                <!-- Left Column (65%): Package Selection -->
                <div class="package-panel">
                    <div class="package-panel-header">
                        <div>
                            <h2 class="package-panel-title">
                                <i class="fa-solid fa-list-check" style="color: var(--accent-cyan);"></i>
                                {{ __('shop.service_select_packages') }}
                                <span class="package-count-badge">{{ $validPackages->count() }} gói</span>
                            </h2>
                            <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 4px;">
                                <i class="fa-solid fa-circle-info" style="color: var(--accent-amber);"></i> Cho phép chọn tối đa <strong>3 mục cày thuê</strong> cùng 1 lúc trên 1 đơn.
                                <span id="selectedCountBadge" style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 12px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34d399; font-weight: 700; font-size: 0.8rem; margin-left: 6px;">
                                    Đã chọn: <span id="selectedCountNum">1</span>/3
                                </span>
                            </div>
                        </div>

                        <!-- Instant Filter Search -->
                        <div style="position: relative;">
                            <input type="text" id="packageFilterInput" class="package-search-input" placeholder="🔍 Tìm kiếm gói..." aria-label="Tìm kiếm gói cày thuê">
                        </div>
                    </div>

                    <!-- Package List -->
                    <div class="package-list" id="packageListContainer">
                        @forelse($validPackages as $index => $package)
                            <div class="package-item {{ $index === 0 ? 'selected' : '' }}"
                                 data-id="{{ $package->id }}"
                                 data-name="{{ $package->name }}"
                                 data-desc="{{ $package->description }}"
                                 data-price="{{ $package->price }}"
                                 role="button"
                                 tabindex="0"
                                 onclick="togglePackage(this)">
                                <div class="package-item-left">
                                    <div class="package-checkbox" style="width: 22px; height: 22px; border-radius: 6px; border: 2px solid rgba(255,255,255,0.25); background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;">
                                        <i class="fa-solid fa-check package-check-icon" style="color: #fff; font-size: 12px; display: {{ $index === 0 ? 'block' : 'none' }};"></i>
                                    </div>
                                    <div>
                                        <div class="package-name">{{ $package->name }}</div>
                                        @if($package->description)
                                            <div class="package-desc">{{ $package->description }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="package-price">{{ number_format($package->price, 0, ',', '.') }} đ</div>
                            </div>
                        @empty
                            <div class="package-empty-state">
                                <i class="fa-solid fa-hourglass-half" style="font-size: 2rem; color: var(--accent-amber); margin-bottom: 10px; display: block;"></i>
                                Giá dịch vụ đang được cập nhật. Hiện chưa có gói dịch vụ khả dụng.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Right Column (35%): Sticky Checkout Form -->
                <aside class="checkout-sticky-card">
                    @if(!$hasValidPrice)
                        {{-- Khóa đặt hàng khi chưa có gói giá hợp lệ --}}
                        <h2 class="checkout-card-title">
                            <i class="fa-solid fa-cart-shopping" style="color: var(--accent-amber);"></i>
                            Đơn Cày Thuê
                        </h2>
                        <div class="package-empty-state" style="margin: 20px 0;">
                            <i class="fa-solid fa-lock" style="font-size: 2rem; color: var(--accent-amber); margin-bottom: 10px; display: block;"></i>
                            Giá dịch vụ đang được cập nhật. Tính năng đặt dịch vụ tạm khóa — vui lòng quay lại sau hoặc liên hệ hỗ trợ.
                        </div>
                    @else
                    <h2 class="checkout-card-title">
                        <i class="fa-solid fa-cart-shopping" style="color: var(--accent-amber);"></i>
                        Đơn Cày Thuê
                    </h2>

                    <!-- Selected Package Summary Box -->
                    <div class="selected-package-preview">
                        <div class="preview-label">DỊCH VỤ ĐÃ CHỌN (<span id="previewCountLabel">1</span>/3):</div>
                        <div class="preview-name" id="previewPackageName" style="white-space: normal; line-height: 1.4;">
                            {{ $service->packages->first()->name ?? 'Chưa chọn dịch vụ' }}
                        </div>
                        <div class="preview-price" id="previewPackagePrice">
                            {{ number_format($service->packages->first()->price ?? 0, 0, ',', '.') }} đ
                        </div>
                    </div>

                    <!-- Order Form -->
                    <form action="{{ route('service.order', $service->slug) }}" method="POST" id="serviceOrderForm">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        
                        <!-- Dynamic Selected Package IDs (Up to 3) -->
                        <div id="packageIdsInputs">
                            <input type="hidden" name="package_ids[]" value="{{ $service->packages->first()->id ?? '' }}">
                        </div>

                        <!-- Game Account -->
                        <div class="form-group-gaming">
                            <label for="game_account" class="form-label-gaming">
                                <i class="fa-solid fa-user"></i> Tài khoản game
                            </label>
                            <input type="text" id="game_account" name="game_account" class="form-control-gaming" 
                                   value="{{ old('game_account') }}" 
                                   placeholder="Nhập tài khoản đăng nhập game" required autocomplete="off">
                        </div>

                        <!-- Game Password with Show/Hide Toggle -->
                        <div class="form-group-gaming">
                            <label for="game_password" class="form-label-gaming">
                                <i class="fa-solid fa-lock"></i> Mật khẩu game
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password" id="game_password" name="game_password" class="form-control-gaming" 
                                       placeholder="Nhập mật khẩu game" required autocomplete="new-password">
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()" aria-label="Hiện mật khẩu">
                                    <i class="fa-solid fa-eye" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                            <div class="form-hint-gaming">Mật khẩu được lưu trữ mã hóa tuyệt đối.</div>
                        </div>

                        <!-- 2FA / Backup Code / Note (appended to note field) -->
                        <div class="form-group-gaming">
                            <label for="two_factor_code" class="form-label-gaming">
                                <i class="fa-solid fa-key"></i> Mã 2FA / Mã xác thực (Nếu có)
                            </label>
                            <input type="text" id="two_factor_code" class="form-control-gaming" 
                                   placeholder="Bỏ trống nếu tài khoản không bật 2FA">
                            <div class="form-hint-gaming">Nên dùng mã dự phòng (backup codes) nếu có.</div>
                        </div>

                        <!-- Note for Booster -->
                        <div class="form-group-gaming" style="margin-bottom: 16px;">
                            <label for="note" class="form-label-gaming">
                                <i class="fa-solid fa-message"></i> Ghi chú cho kỹ thuật viên
                            </label>
                            <textarea id="note" name="note" class="form-control-gaming" rows="2" style="height: auto; padding: 8px 12px;" 
                                      placeholder="Yêu cầu riêng hoặc lưu ý thêm...">{{ old('note') }}</textarea>
                        </div>

                        <!-- User Balance Info -->
                        @if(Auth::check())
                            <div class="user-balance-bar" style="margin-bottom: 16px;">
                                <span>Số dư tài khoản:</span>
                                <span class="user-balance-amount">{{ number_format(Auth::user()->balance) }} đ</span>
                            </div>
                        @endif

                        @if(isset($activeOrder) && $activeOrder)
                            <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; border-radius: 12px; padding: 14px; text-align: center; color: #f87171; font-weight: 700; font-size: 0.95rem; margin-top: 16px; margin-bottom: 16px;">
                                <i class="fa-solid fa-ban me-1"></i> Đang có đơn cày thuê chưa hoàn tất (#{{ $activeOrder->id }}). Tạm khóa đặt đơn mới!
                            </div>
                        @else
                        <div class="checkout-u230-container" style="margin-top: 12px;">
                            <x-order-summary-u230 
                                id="serviceCheckoutSummary"
                                title="Đơn Cày Thuê"
                                :items="[
                                    [$service->name, 'Gói: ' . ($validPackages->first()->name ?? ''), (float)($validPackages->first()->price ?? 0), $service->thumbnail ?: 'linear-gradient(140deg,#E0E7FF,#A5B4FC)'],
                                    ['Bảo mật & Hỗ trợ kỹ thuật', 'Bảo hiểm 100% · Tự động 24/7', 0, 'linear-gradient(140deg,#FEF3C7,#FCD34D)']
                                ]"
                                shipping="0"
                                shippingLabel="Phí xử lý hệ thống"
                                currency="đ"
                                promoCode="WELCOME10"
                                discountPercent="10"
                                buttonText="{{ Auth::check() ? 'Xác Nhận Đặt Đơn' : __('shop.nav_login') }}"
                                formId="{{ Auth::check() ? 'serviceOrderForm' : '' }}"
                                inputName="giftcode"
                                loginUrl="{{ Auth::check() ? '' : route('login') }}"
                            />
                        </div>
                        @endif
                    </form>
                    @endif
                </aside>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Selected Packages Array (Max 3)
    let selectedPackages = [];
    @if(isset($validPackages) && $validPackages->count() > 0)
        selectedPackages.push({
            id: "{{ $validPackages->first()->id }}",
            name: "{{ addslashes($validPackages->first()->name) }}",
            price: {{ (int)$validPackages->first()->price }}
        });
    @endif

    function updatePackageSelectionUI() {
        const previewName = document.getElementById('previewPackageName');
        const previewPrice = document.getElementById('previewPackagePrice');
        const previewCountLabel = document.getElementById('previewCountLabel');
        const selectedCountNum = document.getElementById('selectedCountNum');
        const packageIdsInputs = document.getElementById('packageIdsInputs');

        let totalPrice = 0;
        let names = [];
        let inputsHtml = '';

        selectedPackages.forEach(function(pkg, index) {
            totalPrice += pkg.price;
            names.push((selectedPackages.length > 1 ? (index + 1) + '. ' : '') + pkg.name);
            inputsHtml += '<input type="hidden" name="package_ids[]" value="' + pkg.id + '">';
        });

        if (previewName) previewName.innerText = names.join(' · ');
        if (previewPrice) previewPrice.innerText = totalPrice.toLocaleString('vi-VN') + ' đ';
        if (previewCountLabel) previewCountLabel.innerText = selectedPackages.length;
        if (selectedCountNum) selectedCountNum.innerText = selectedPackages.length;
        if (packageIdsInputs) packageIdsInputs.innerHTML = inputsHtml;

        // Update u230 order summary component dynamically
        var summaryElem = document.getElementById('serviceCheckoutSummary');
        if (summaryElem && typeof summaryElem.u230Update === 'function') {
            var summaryItems = selectedPackages.map(function(pkg) {
                return ['{{ $service->name }}', 'Gói: ' + pkg.name, pkg.price, '{{ $service->thumbnail ?: "linear-gradient(140deg,#E0E7FF,#A5B4FC)" }}'];
            });
            summaryItems.push(['Bảo mật & Hỗ trợ kỹ thuật', 'Bảo hiểm 100% · Tự động 24/7', 0, 'linear-gradient(140deg,#FEF3C7,#FCD34D)']);
            summaryElem.u230Update(summaryItems, 0);
        }

        // Stepper state
        var stepper = document.getElementById('serviceCheckoutStepper');
        if (stepper && typeof stepper.setActiveStep === 'function') {
            stepper.setActiveStep(2);
        }
    }

    function togglePackage(elem) {
        const pkgId = elem.dataset.id;
        const pkgName = elem.dataset.name;
        const pkgPrice = parseInt(elem.dataset.price) || 0;
        const checkIcon = elem.querySelector('.package-check-icon');

        const existingIndex = selectedPackages.findIndex(p => String(p.id) === String(pkgId));

        if (existingIndex > -1) {
            // Uncheck
            if (selectedPackages.length === 1) {
                if (typeof FuiToast !== 'undefined') {
                    FuiToast.warning('Vui lòng chọn ít nhất 1 gói cày thuê.');
                } else {
                    alert('Vui lòng chọn ít nhất 1 gói cày thuê.');
                }
                return;
            }
            selectedPackages.splice(existingIndex, 1);
            elem.classList.remove('selected');
            if (checkIcon) checkIcon.style.display = 'none';
        } else {
            // Check
            if (selectedPackages.length >= 3) {
                if (typeof FuiToast !== 'undefined') {
                    FuiToast.error('Chỉ được chọn tối đa 3 mục cày thuê cùng 1 lúc trên 1 đơn.');
                } else {
                    alert('Chỉ được chọn tối đa 3 mục cày thuê cùng 1 lúc trên 1 đơn.');
                }
                return;
            }
            selectedPackages.push({
                id: pkgId,
                name: pkgName,
                price: pkgPrice
            });
            elem.classList.add('selected');
            if (checkIcon) checkIcon.style.display = 'block';
        }

        updatePackageSelectionUI();
    }

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('game_password');
        const icon = document.getElementById('passwordToggleIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Real-time package search filter
    document.getElementById('packageFilterInput')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const items = document.querySelectorAll('.package-item');
        items.forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const desc = (item.dataset.desc || '').toLowerCase();
            if (name.includes(query) || desc.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Append 2FA code to note before submitting
    var orderForm = document.getElementById('serviceOrderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', function() {
            var twoFaInput = document.getElementById('two_factor_code');
            var twoFa = twoFaInput ? twoFaInput.value.trim() : '';
            var noteArea = document.getElementById('note');
            if (twoFa && noteArea) {
                noteArea.value = (noteArea.value ? noteArea.value + ' | ' : '') + '2FA/Backup Code: ' + twoFa;
            }
        });
    }

    // Initialize UI on page load
    document.addEventListener('DOMContentLoaded', function() {
        updatePackageSelectionUI();
    });
</script>
<style>
    .package-empty-state {
        text-align: center;
        padding: 40px 24px;
        color: var(--text-muted, #8A94A6);
        font-size: 0.95rem;
        line-height: 1.6;
        background: rgba(245, 158, 11, 0.06);
        border: 1px dashed rgba(245, 158, 11, 0.35);
        border-radius: 12px;
        margin: 8px;
    }
</style>
@endpush
