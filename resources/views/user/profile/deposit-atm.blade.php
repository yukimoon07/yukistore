@extends('layouts.user.app')

@section('title', $title)

@section('content')

    <section class="profile-section">
        <div class="container">
            <div class="profile-container">
                <div class="profile-header">
                    <h1 class="profile-title">NẠP TIỀN QUA NGÂN HÀNG</h1>
                </div>

                <div class="profile-content">
                    @include('layouts.user.sidebar')

                    <div class="profile-main">
                        <div class="profile-info-card">
                            <div class="info-header">
                                <div class="balance-info">
                                    <span class="balance-label">SỐ DƯ:</span>
                                    <span class="balance-value">{{ number_format(Auth::user()->balance ?? 0) }} VND</span>
                                </div>
                            </div>

                            <div class="info-content">
                                <!-- Thông báo -->
                                @if ($errors->any())
                                    <div class="service__alert service__alert--error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <div>
                                            <span>Đã có lỗi xảy ra:</span>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <button type="button" class="service__alert-close">&times;</button>
                                    </div>
                                @endif
                                @foreach (['error', 'success'] as $msg)
                                    @if (session($msg))
                                        <div
                                            class="service__alert service__alert--{{ $msg === 'error' ? 'error' : 'success' }}">
                                            <i
                                                class="fas fa-{{ $msg === 'error' ? 'exclamation-circle' : 'check-circle' }}"></i>
                                            <div>
                                                <span>{{ session($msg) }}</span>
                                            </div>
                                            <button type="button" class="service__alert-close">&times;</button>
                                        </div>
                                    @endif
                                @endforeach
                                <!-- Kết thúc thông báo -->

                                <style>
                                    .deposit-grid { display: grid; grid-template-columns: 1fr; gap: 24px; margin-top: 20px; }
                                    @media (min-width: 992px) { .deposit-grid { grid-template-columns: 350px 1fr; } }
                                    
                                    .d-box { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
                                    .d-box-title { font-weight: 700; font-size: 1.1rem; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
                                    
                                    .promo-box { border-color: #fef08a; background: #fefce8; }
                                    .promo-box .d-box-title { color: #854d0e; }
                                    .promo-tags { display: flex; gap: 12px; flex-wrap: wrap; }
                                    .promo-tag { background: rgba(234, 179, 8, 0.2); color: #854d0e; font-size: 0.85rem; padding: 6px 12px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(234, 179, 8, 0.4); }
                                    
                                    .step-list { display: flex; flex-direction: column; gap: 16px; }
                                    .step-item { display: flex; gap: 16px; }
                                    .step-num { width: 28px; height: 28px; border-radius: 50%; background: #ef4444; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
                                    .step-content h4 { margin: 0 0 4px 0; font-size: 1rem; color: #1e293b; font-weight: 600; }
                                    .step-content p { margin: 0; font-size: 0.85rem; color: #64748b; }
                                    
                                    .bank-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
                                    .bank-opt { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; cursor: pointer; transition: 0.2s; display: flex; flex-direction: row; align-items: center; gap: 12px; }
                                    .bank-opt:hover { border-color: #cbd5e1; background: #f8fafc; }
                                    .bank-opt.active { border-color: #ef4444; background: #fef2f2; box-shadow: 0 0 0 1px #ef4444; }
                                    .bank-opt-img { width: 40px; height: 40px; border-radius: 4px; flex-shrink: 0; background: #fff; display: flex; align-items: center; justify-content: center; padding: 2px; border: 1px solid #eee; }
                                    .bank-opt-img img { max-width: 100%; max-height: 100%; object-fit: contain; }
                                    .bank-opt-info { display: flex; flex-direction: column; gap: 2px; }
                                    .bank-opt-name { font-weight: 700; color: #1e293b; font-size: 1.05rem; }
                                    .bank-opt-owner { font-size: 0.85rem; color: #64748b; }
                                    
                                    .amount-input-group { margin-bottom: 16px; }
                                    .amount-input-group label { display: block; font-weight: 600; color: #1e293b; margin-bottom: 8px; font-size: 0.95rem; }
                                    .amount-input { width: 100%; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 1rem; outline: none; transition: 0.2s; background: #f8fafc; }
                                    .amount-input:focus { border-color: #ef4444; background: #fff; }
                                    
                                    .quick-amounts { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
                                    .btn-quick { background: #fff; border: 1px solid #e5e7eb; padding: 8px 16px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; color: #475569; cursor: pointer; transition: 0.2s; }
                                    .btn-quick:hover, .btn-quick.active { border-color: #ef4444; color: #ef4444; background: #fef2f2; }
                                    
                                    .btn-submit { width: 100%; background: #ef4444; color: #fff; border: none; padding: 14px; border-radius: 8px; font-size: 1.05rem; font-weight: 700; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
                                    .btn-submit:hover { background: #dc2626; }
                                    .btn-submit:disabled { background: #cbd5e1; cursor: not-allowed; }
                                    
                                    /* QR Result & Countdown */
                                    .qr-countdown-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); transition: all 0.3s ease; }
                                    .qr-countdown-box.is-urgent { background: #fff1f2; border-color: #fecdd3; }
                                    .qr-countdown-box.is-expired { background: #fef2f2; border-color: #fca5a5; }
                                    .countdown-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 8px; }
                                    .countdown-title { font-size: 0.95rem; font-weight: 700; color: #92400e; display: flex; align-items: center; gap: 8px; }
                                    .is-urgent .countdown-title { color: #e11d48; }
                                    .is-expired .countdown-title { color: #b91c1c; }
                                    .countdown-timer { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 1.25rem; font-weight: 800; color: #b45309; background: #fef3c7; padding: 4px 12px; border-radius: 8px; border: 1px solid #fde68a; letter-spacing: 1px; }
                                    .is-urgent .countdown-timer { color: #e11d48; background: #ffe4e6; border-color: #fecdd3; animation: countdownPulse 1s infinite alternate; }
                                    .is-expired .countdown-timer { color: #b91c1c; background: #fee2e2; border-color: #fca5a5; animation: none; }
                                    @keyframes countdownPulse { from { transform: scale(1); } to { transform: scale(1.05); } }
                                    .countdown-progress-track { width: 100%; height: 6px; background: #fde68a; border-radius: 9999px; overflow: hidden; margin-bottom: 8px; }
                                    .is-urgent .countdown-progress-track { background: #fecdd3; }
                                    .is-expired .countdown-progress-track { background: #fca5a5; }
                                    .countdown-progress-bar { height: 100%; width: 100%; background: linear-gradient(90deg, #f59e0b, #ea580c); border-radius: 9999px; transition: width 1s linear; }
                                    .is-urgent .countdown-progress-bar { background: linear-gradient(90deg, #f43f5e, #e11d48); }
                                    .is-expired .countdown-progress-bar { width: 0% !important; background: #b91c1c; }
                                    .countdown-note { font-size: 0.82rem; color: #78350f; display: flex; align-items: center; gap: 6px; }
                                    .is-urgent .countdown-note { color: #9f1239; }
                                    .is-expired .countdown-note { color: #991b1b; }

                                    .qr-result { display: flex; flex-direction: column; gap: 24px; align-items: center; text-align: center; }
                                    @media (min-width: 768px) { .qr-result { flex-direction: row; text-align: left; align-items: flex-start; } }
                                    .qr-image-wrapper { display: flex; flex-direction: column; align-items: center; gap: 12px; width: 250px; flex-shrink: 0; }
                                    .qr-image { background: #fff; padding: 14px; border-radius: 12px; border: 1px solid #e5e7eb; width: 100%; cursor: pointer; position: relative; transition: all 0.2s; overflow: hidden; }
                                    .qr-image:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.08); border-color: #cbd5e1; }
                                    .qr-image img { width: 100%; height: auto; display: block; border-radius: 8px; transition: filter 0.3s ease, opacity 0.3s ease; }
                                    .qr-image.is-expired { cursor: not-allowed; transform: none; box-shadow: none; }
                                    .qr-image.is-expired img { filter: blur(6px) grayscale(85%); opacity: 0.2; }
                                    .qr-overlay-hint { position: absolute; inset: 0; background: rgba(0,0,0,0.5); color: #fff; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 0.85rem; font-weight: 600; opacity: 0; transition: opacity 0.2s; border-radius: 12px; pointer-events: none; }
                                    .qr-image:not(.is-expired):hover .qr-overlay-hint { opacity: 1; }

                                    /* Expired Overlay */
                                    .qr-expired-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.94); backdrop-filter: blur(3px); display: none; flex-direction: column; align-items: center; justify-content: center; padding: 16px; text-align: center; z-index: 10; border-radius: 12px; }
                                    .qr-image.is-expired .qr-expired-overlay { display: flex; }
                                    .expired-icon { font-size: 2.2rem; color: #ef4444; margin-bottom: 6px; }
                                    .expired-title { font-size: 0.92rem; font-weight: 800; color: #f87171; letter-spacing: 0.5px; margin-bottom: 4px; }
                                    .expired-desc { font-size: 0.76rem; color: #cbd5e1; margin-bottom: 12px; line-height: 1.35; }
                                    .btn-renew-qr { background: #ef4444; color: #fff; border: none; padding: 7px 14px; border-radius: 6px; font-size: 0.82rem; font-weight: 700; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 6px; }
                                    .btn-renew-qr:hover { background: #dc2626; transform: scale(1.02); }

                                    /* QR Action Buttons Group */
                                    .qr-actions-group { display: flex; gap: 8px; width: 100%; }
                                    .btn-qr-action { flex: 1; padding: 9px 10px; border-radius: 8px; font-size: 0.86rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 6px; border: none; text-decoration: none; }
                                    .btn-download-qr { background: #2563eb; color: #fff; }
                                    .btn-download-qr:hover:not(:disabled) { background: #1d4ed8; }
                                    .btn-zoom-qr { background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; }
                                    .btn-zoom-qr:hover:not(:disabled) { background: #e2e8f0; color: #0f172a; border-color: #94a3b8; }
                                    .btn-qr-action:disabled { background: #e2e8f0; color: #94a3b8; border-color: #cbd5e1; cursor: not-allowed; opacity: 0.6; }

                                    /* Modal phong to QR */
                                    .qr-modal { display: none; position: fixed; z-index: 99999; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(6px); align-items: center; justify-content: center; padding: 16px; }
                                    .qr-modal.active { display: flex; }
                                    .qr-modal-content { background: #fff; border-radius: 16px; max-width: 440px; width: 100%; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.3); animation: qrPopIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
                                    .qr-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #e5e7eb; }
                                    .qr-modal-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }
                                    .qr-modal-close { background: none; border: none; font-size: 1.6rem; line-height: 1; color: #64748b; cursor: pointer; padding: 4px 8px; border-radius: 6px; transition: 0.2s; }
                                    .qr-modal-close:hover { background: #f1f5f9; color: #0f172a; }
                                    .qr-modal-body { padding: 20px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 14px; }
                                    .modal-timer-badge { background: #fef3c7; color: #b45309; font-size: 0.85rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid #fde68a; display: inline-flex; align-items: center; gap: 6px; }
                                    .modal-timer-badge.is-urgent { background: #ffe4e6; color: #e11d48; border-color: #fecdd3; }
                                    .modal-timer-badge.is-expired { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
                                    .qr-modal-img { width: 100%; max-width: 320px; height: auto; border-radius: 12px; background: #fff; border: 1px solid #e2e8f0; padding: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
                                    .qr-modal-info { width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 0.88rem; display: flex; justify-content: space-around; color: #334155; }
                                    .qr-modal-actions { display: flex; gap: 10px; width: 100%; }
                                    .btn-download-modal { flex: 1; background: #2563eb; color: #fff; border: none; padding: 11px 16px; border-radius: 8px; font-weight: 700; font-size: 0.92rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.2s; }
                                    .btn-download-modal:hover:not(:disabled) { background: #1d4ed8; }
                                    .btn-download-modal:disabled { background: #94a3b8; cursor: not-allowed; }
                                    .btn-close-modal { background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; padding: 11px 16px; border-radius: 8px; font-weight: 600; font-size: 0.92rem; cursor: pointer; transition: 0.2s; }
                                    .btn-close-modal:hover { background: #e2e8f0; color: #0f172a; }
                                    .qr-modal-hint { font-size: 0.82rem; color: #64748b; }
                                    @keyframes qrPopIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

                                    .qr-info { flex: 1; width: 100%; }
                                    .info-row-qr { display: flex; flex-direction: column; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed #e5e7eb; }
                                    .info-row-qr:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
                                    .info-row-qr span.lbl { font-size: 0.85rem; color: #64748b; margin-bottom: 4px; }
                                    .info-row-qr span.val { font-size: 1.05rem; font-weight: 700; color: #1e293b; display: flex; justify-content: space-between; align-items: center; }
                                    .info-row-qr span.val .copy-btn { background: #e2e8f0; color: #475569; border: none; padding: 4px 12px; border-radius: 4px; font-size: 0.8rem; cursor: pointer; font-weight: 600; }
                                    .info-row-qr span.val .copy-btn:hover { background: #cbd5e1; }
                                    .info-row-qr span.val .copy-btn:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
                                    
                                    /* Dark mode */
                                    [data-theme="dark"] .d-box { background: #171717; border-color: #2a2a2a; }
                                    [data-theme="dark"] .d-box-title { color: #f8fafc; }
                                    [data-theme="dark"] .promo-box { background: rgba(133, 77, 14, 0.1); border-color: rgba(234, 179, 8, 0.2); }
                                    [data-theme="dark"] .promo-box .d-box-title { color: #fde047; }
                                    [data-theme="dark"] .promo-tag { color: #fef08a; }
                                    [data-theme="dark"] .step-content h4 { color: #f8fafc; }
                                    [data-theme="dark"] .step-content p { color: #94a3b8; }
                                    [data-theme="dark"] .bank-opt { border-color: #333; background: #262626; }
                                    [data-theme="dark"] .bank-opt:hover { border-color: #404040; background: #333; }
                                    [data-theme="dark"] .bank-opt.active { border-color: #ef4444; background: rgba(239, 68, 68, 0.1); }
                                    [data-theme="dark"] .bank-opt-name { color: #f8fafc; }
                                    [data-theme="dark"] .bank-opt-owner { color: #94a3b8; }
                                    [data-theme="dark"] .amount-input-group label { color: #f8fafc; }
                                    [data-theme="dark"] .amount-input { background: #262626; border-color: #333; color: #f8fafc; }
                                    [data-theme="dark"] .amount-input:focus { border-color: #ef4444; }
                                    [data-theme="dark"] .btn-quick { background: #262626; border-color: #333; color: #cbd5e1; }
                                    [data-theme="dark"] .btn-quick:hover, [data-theme="dark"] .btn-quick.active { border-color: #ef4444; color: #ef4444; background: rgba(239, 68, 68, 0.1); }
                                    [data-theme="dark"] .btn-submit:disabled { background: #404040; color: #6b7280; }
                                    [data-theme="dark"] .qr-image { background: #262626; border-color: #333; }
                                    [data-theme="dark"] .qr-countdown-box { background: rgba(245, 158, 11, 0.08); border-color: rgba(245, 158, 11, 0.25); }
                                    [data-theme="dark"] .qr-countdown-box.is-urgent { background: rgba(244, 63, 94, 0.1); border-color: rgba(244, 63, 94, 0.3); }
                                    [data-theme="dark"] .qr-countdown-box.is-expired { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.35); }
                                    [data-theme="dark"] .countdown-title { color: #fde047; }
                                    [data-theme="dark"] .is-urgent .countdown-title { color: #fda4af; }
                                    [data-theme="dark"] .is-expired .countdown-title { color: #fca5a5; }
                                    [data-theme="dark"] .countdown-timer { background: rgba(245, 158, 11, 0.2); color: #fde047; border-color: rgba(245, 158, 11, 0.4); }
                                    [data-theme="dark"] .is-urgent .countdown-timer { background: rgba(244, 63, 94, 0.2); color: #fda4af; border-color: rgba(244, 63, 94, 0.4); }
                                    [data-theme="dark"] .is-expired .countdown-timer { background: rgba(239, 68, 68, 0.2); color: #fca5a5; border-color: rgba(239, 68, 68, 0.4); }
                                    [data-theme="dark"] .countdown-progress-track { background: #333; }
                                    [data-theme="dark"] .is-urgent .countdown-progress-track { background: #401018; }
                                    [data-theme="dark"] .is-expired .countdown-progress-track { background: #451010; }
                                    [data-theme="dark"] .countdown-note { color: #fef08a; }
                                    [data-theme="dark"] .is-urgent .countdown-note { color: #fecdd3; }
                                    [data-theme="dark"] .is-expired .countdown-note { color: #fecaca; }
                                    [data-theme="dark"] .btn-zoom-qr { background: #262626; border-color: #404040; color: #e2e8f0; }
                                    [data-theme="dark"] .btn-zoom-qr:hover:not(:disabled) { background: #333333; border-color: #525252; color: #fff; }
                                    [data-theme="dark"] .btn-qr-action:disabled { background: #262626; color: #64748b; border-color: #333; }
                                    [data-theme="dark"] .qr-modal-content { background: #1c1c1c; border: 1px solid #333; }
                                    [data-theme="dark"] .qr-modal-header { border-bottom-color: #333; }
                                    [data-theme="dark"] .qr-modal-title { color: #f8fafc; }
                                    [data-theme="dark"] .qr-modal-close:hover { background: #333; color: #fff; }
                                    [data-theme="dark"] .qr-modal-info { background: #262626; border-color: #333; color: #cbd5e1; }
                                    [data-theme="dark"] .btn-close-modal { background: #262626; border-color: #404040; color: #cbd5e1; }
                                    [data-theme="dark"] .btn-close-modal:hover { background: #333; color: #fff; }
                                    [data-theme="dark"] .qr-modal-hint { color: #94a3b8; }
                                    [data-theme="dark"] .info-row-qr { border-bottom-color: #333; }
                                    [data-theme="dark"] .info-row-qr span.lbl { color: #94a3b8; }
                                    [data-theme="dark"] .info-row-qr span.val { color: #f8fafc; }
                                    [data-theme="dark"] .info-row-qr span.val .copy-btn { background: #334155; color: #f8fafc; }
                                    [data-theme="dark"] .info-row-qr span.val .copy-btn:hover { background: #475569; }
                                    [data-theme="dark"] .info-row-qr span.val .copy-btn:disabled { background: #262626; color: #64748b; }
                                </style>

                                <div class="deposit-grid">
                                    <!-- Left Column -->
                                    <div class="deposit-left">
                                        <div class="d-box promo-box">
                                            <div class="d-box-title"><i class="fa-solid fa-gift"></i> Khuyến mãi nạp bank</div>
                                            <div class="promo-tags">
                                                <div class="promo-tag">Nạp từ 100.000đ &rarr; +5%</div>
                                                <div class="promo-tag">Nạp từ 1.000.000đ &rarr; +10%</div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-box">
                                            <div class="d-box-title"><i class="fa-solid fa-lightbulb"></i> Hướng dẫn nạp tiền</div>
                                            <div class="step-list">
                                                <div class="step-item">
                                                    <div class="step-num">1</div>
                                                    <div class="step-content">
                                                        <h4>Tạo hoá đơn</h4>
                                                        <p>Chọn ngân hàng và nhập số tiền muốn nạp.</p>
                                                    </div>
                                                </div>
                                                <div class="step-item">
                                                    <div class="step-num">2</div>
                                                    <div class="step-content">
                                                        <h4>Chuyển khoản</h4>
                                                        <p>Quét QR hoặc chuyển đúng số tiền và nội dung CK. Nội dung phải bắt đầu bằng <strong>{{ config('sepay.prefix', 'Yuki') }}{{ str_pad(Auth::id(), 3, '0', STR_PAD_LEFT) }}</strong>.</p>
                                                    </div>
                                                </div>
                                                <div class="step-item">
                                                    <div class="step-num">3</div>
                                                    <div class="step-content">
                                                        <h4>Tự động cộng tiền</h4>
                                                        <p>Hệ thống xác nhận và cộng tiền tự động qua webhook HMAC-SHA256 sau khi SePay gửi giao dịch hợp lệ.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="deposit-right">
                                        <div class="d-box" id="step-form">
                                            <div class="d-box-title"><i class="fa-solid fa-file-invoice"></i> Tạo hoá đơn nạp tiền</div>
                                            
                                            <div class="amount-input-group">
                                                <label>Chọn ngân hàng</label>
                                                <div class="bank-options">
                                                    @if(count($bankAccounts) > 0)
                                                        @foreach($bankAccounts as $account)
                                                            <div class="bank-opt" data-bank="{{ $account->bank_name }}" data-acc="{{ $account->account_number }}" data-name="{{ $account->account_name }}" data-prefix="{{ $account->prefix }}">
                                                                @if($account->image)
                                                                    <div class="bank-opt-img">
                                                                        <img src="{{ asset($account->image) }}" alt="{{ $account->bank_name }}">
                                                                    </div>
                                                                @endif
                                                                <div class="bank-opt-info">
                                                                    <div class="bank-opt-name">{{ $account->bank_name }}</div>
                                                                    <div class="bank-opt-owner">{{ $account->account_name }}</div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p class="text-danger">Hiện tại chưa có tài khoản ngân hàng nào.</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="amount-input-group">
                                                <label>Số tiền nạp</label>
                                                <input type="number" id="deposit-amount" class="amount-input" placeholder="Nhập số tiền muốn nạp">
                                            </div>
                                            
                                            <div class="quick-amounts">
                                                <button type="button" class="btn-quick" data-val="50000">50K</button>
                                                <button type="button" class="btn-quick" data-val="100000">100K</button>
                                                <button type="button" class="btn-quick" data-val="200000">200K</button>
                                                <button type="button" class="btn-quick" data-val="500000">500K</button>
                                                <button type="button" class="btn-quick" data-val="1000000">1M</button>
                                            </div>

                                            <button id="btn-create-invoice" class="btn-submit" disabled>
                                                <i class="fa-solid fa-file-invoice"></i> Tạo hoá đơn
                                            </button>
                                        </div>

                                        <!-- QR Result Step -->
                                        <div class="d-box" id="step-qr" style="display: none;">
                                            <div class="d-box-title"><i class="fa-solid fa-qrcode"></i> Quét mã thanh toán</div>

                                            <!-- Countdown Box (15 phút) -->
                                            <div class="qr-countdown-box" id="qr-countdown-box">
                                                <div class="countdown-header">
                                                    <span class="countdown-title">
                                                        <i class="fa-solid fa-clock"></i> Đơn nạp hết hạn sau:
                                                    </span>
                                                    <span class="countdown-timer" id="countdown-timer">15:00</span>
                                                </div>
                                                <div class="countdown-progress-track">
                                                    <div class="countdown-progress-bar" id="countdown-progress-bar"></div>
                                                </div>
                                                <div class="countdown-note">
                                                    <i class="fa-solid fa-circle-info"></i> Mã QR có hiệu lực trong 15 phút. Quét mã bằng ứng dụng ngân hàng để nạp tự động.
                                                </div>
                                            </div>

                                            <div class="qr-result">
                                                <div class="qr-image-wrapper">
                                                    <div class="qr-image" id="qr-img-box" title="Bấm để phóng to">
                                                        <img id="qr-img" src="" alt="QR Code">
                                                        <div class="qr-overlay-hint">
                                                            <i class="fas fa-search-plus"></i> Bấm để phóng to
                                                        </div>
                                                        <div class="qr-expired-overlay" id="qr-expired-overlay">
                                                            <div class="expired-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                                                            <div class="expired-title">MÃ QR ĐÃ HẾT HẠN</div>
                                                            <div class="expired-desc">Hoá đơn đã quá thời hạn 15 phút. Vui lòng tạo mã QR mới để thanh toán.</div>
                                                            <button type="button" class="btn-renew-qr" id="btn-renew-qr">
                                                                <i class="fa-solid fa-rotate-right"></i> Tạo mã mới
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Action buttons: Tải về & Phóng to -->
                                                    <div class="qr-actions-group">
                                                        <button type="button" id="btn-download-qr" class="btn-qr-action btn-download-qr" title="Tải ảnh QR về máy">
                                                            <i class="fa-solid fa-download"></i> Tải mã QR
                                                        </button>
                                                        <button type="button" id="btn-zoom-qr" class="btn-qr-action btn-zoom-qr" title="Phóng to mã QR">
                                                            <i class="fa-solid fa-expand"></i> Phóng to
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="qr-info">
                                                    <div class="info-row-qr">
                                                        <span class="lbl">Ngân hàng</span>
                                                        <span class="val" id="qr-bank"></span>
                                                    </div>
                                                    <div class="info-row-qr">
                                                        <span class="lbl">Chủ tài khoản</span>
                                                        <span class="val" id="qr-name"></span>
                                                    </div>
                                                    <div class="info-row-qr">
                                                        <span class="lbl">Số tài khoản</span>
                                                        <span class="val">
                                                            <span id="qr-acc"></span>
                                                            <button class="copy-btn" id="copy-acc" data-clipboard-text=""><i class="far fa-copy"></i> Copy</button>
                                                        </span>
                                                    </div>
                                                    <div class="info-row-qr">
                                                        <span class="lbl">Số tiền</span>
                                                        <span class="val" style="color: #ef4444;">
                                                            <span id="qr-amount"></span>
                                                            <button class="copy-btn" id="copy-amt" data-clipboard-text=""><i class="far fa-copy"></i> Copy</button>
                                                        </span>
                                                    </div>
                                                    <div class="info-row-qr">
                                                        <span class="lbl">Nội dung chuyển khoản (Bắt buộc)</span>
                                                        <span class="val" style="color: #ef4444;">
                                                            <span id="qr-content"></span>
                                                            <button class="copy-btn" id="copy-content" data-clipboard-text=""><i class="far fa-copy"></i> Copy</button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <button id="btn-back" class="btn-submit" style="margin-top: 24px; background: #64748b;">
                                                <i class="fa-solid fa-arrow-left"></i> Tạo hoá đơn khác
                                            </button>
                                        </div>

                                        <!-- Modal phóng to mã QR -->
                                        <div id="qr-modal" class="qr-modal">
                                            <div class="qr-modal-content">
                                                <div class="qr-modal-header">
                                                    <div class="qr-modal-title"><i class="fa-solid fa-qrcode"></i> Mã thanh toán VietQR</div>
                                                    <button type="button" class="qr-modal-close" id="qr-modal-close" aria-label="Đóng">&times;</button>
                                                </div>
                                                <div class="qr-modal-body">
                                                    <div class="modal-timer-badge" id="modal-timer-badge">
                                                        <i class="fa-solid fa-clock"></i> Còn lại: <span id="modal-countdown-timer">15:00</span>
                                                    </div>
                                                    <img id="qr-modal-img" class="qr-modal-img" src="" alt="QR Code Phóng to">
                                                    <div class="qr-modal-info">
                                                        <div><strong>Số tiền:</strong> <span id="modal-qr-amount" style="color:#ef4444;font-weight:700;">-</span></div>
                                                        <div><strong>Nội dung:</strong> <span id="modal-qr-content" style="color:#ef4444;font-weight:700;">-</span></div>
                                                    </div>
                                                    <div class="qr-modal-actions">
                                                        <button type="button" id="btn-download-modal" class="btn-download-modal">
                                                            <i class="fa-solid fa-download"></i> Tải mã QR về máy
                                                        </button>
                                                        <button type="button" class="btn-close-modal" id="btn-close-modal-bottom">
                                                            Đóng
                                                        </button>
                                                    </div>
                                                    <div class="qr-modal-hint">
                                                        <i class="fas fa-info-circle"></i> Quét bằng ứng dụng ngân hàng bất kỳ để tự động nhận diện số tiền và nội dung chuyển khoản.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="deposit-history">
                                    <div class="history-header">LỊCH SỬ NẠP TIỀN</div>
                                    <div class="history-table-container">
                                        <table class="history-table">
                                            <thead>
                                                <tr>
                                                    <th>Thời gian</th>
                                                    <th>Số tiền</th>
                                                    <th>Ngân hàng</th>
                                                    <th>Nội dung</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($transactions) && count($transactions) > 0)
                                                    @foreach ($transactions as $transaction)
                                                        <tr>
                                                            <td>{{ $transaction->created_at }}</td>
                                                            <td class="text-success">
                                                                {{ number_format($transaction->amount) }} VND</td>
                                                            <td>{{ $transaction->bank }}</td>
                                                            <td>{{ $transaction->content }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="no-data">Không có dữ liệu</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @if (isset($transactions) && $transactions->hasPages())
                                        <div class="pagination">
                                            {{ $transactions->links('user.pagination.custom') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // UI Elements
                const bankOpts = document.querySelectorAll('.bank-opt');
                const btnQuick = document.querySelectorAll('.btn-quick');
                const inputAmount = document.getElementById('deposit-amount');
                const btnSubmit = document.getElementById('btn-create-invoice');
                const btnBack = document.getElementById('btn-back');
                const btnRenewQr = document.getElementById('btn-renew-qr');
                
                const stepForm = document.getElementById('step-form');
                const stepQr = document.getElementById('step-qr');
                
                const qrImgBox = document.getElementById('qr-img-box');
                const qrImg = document.getElementById('qr-img');
                const btnZoomQr = document.getElementById('btn-zoom-qr');
                const btnDownloadQr = document.getElementById('btn-download-qr');
                
                const qrModal = document.getElementById('qr-modal');
                const qrModalImg = document.getElementById('qr-modal-img');
                const qrModalClose = document.getElementById('qr-modal-close');
                const btnCloseModalBottom = document.getElementById('btn-close-modal-bottom');
                const btnDownloadModal = document.getElementById('btn-download-modal');
                const modalQrAmount = document.getElementById('modal-qr-amount');
                const modalQrContent = document.getElementById('modal-qr-content');

                const countdownBox = document.getElementById('qr-countdown-box');
                const countdownTimer = document.getElementById('countdown-timer');
                const countdownProgressBar = document.getElementById('countdown-progress-bar');
                const modalTimerBadge = document.getElementById('modal-timer-badge');
                const modalCountdownTimer = document.getElementById('modal-countdown-timer');

                let selectedBank = null;
                const userId = "{{ Auth::user()->id ?? '' }}";

                // 15-Minute Countdown State
                const TOTAL_COUNTDOWN_SECONDS = 15 * 60; // 900 seconds
                let remainingSeconds = TOTAL_COUNTDOWN_SECONDS;
                let countdownInterval = null;
                let isExpired = false;

                // Clipboard.js init with expired check
                document.querySelectorAll('.copy-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        if (isExpired) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            if (typeof FuiToast !== 'undefined') {
                                FuiToast.error('Mã QR đã hết hạn! Vui lòng tạo mã QR mới để thanh toán.');
                            } else {
                                alert('Mã QR đã hết hạn! Vui lòng tạo hoá đơn mới để tiếp tục.');
                            }
                            return false;
                        }
                    }, true);
                });

                var clipboard = new ClipboardJS('.copy-btn');

                clipboard.on('success', function(e) {
                    if (isExpired) return;
                    const originalText = e.trigger.innerHTML;
                    e.trigger.innerHTML = '<i class="fas fa-check"></i> Đã chép';

                    if (typeof FuiToast !== 'undefined') {
                        FuiToast.success('Đã sao chép: ' + e.text);
                    }

                    setTimeout(function() {
                        e.trigger.innerHTML = originalText;
                    }, 2000);

                    e.clearSelection();
                });

                function checkSubmit() {
                    const amount = parseInt(inputAmount.value);
                    if (selectedBank && amount >= 10000) {
                        btnSubmit.disabled = false;
                    } else {
                        btnSubmit.disabled = true;
                    }
                }

                bankOpts.forEach(opt => {
                    opt.addEventListener('click', function() {
                        bankOpts.forEach(o => o.classList.remove('active'));
                        this.classList.add('active');
                        selectedBank = {
                            bank: this.dataset.bank,
                            acc: this.dataset.acc,
                            name: this.dataset.name,
                            prefix: this.dataset.prefix
                        };
                        checkSubmit();
                    });
                });

                btnQuick.forEach(btn => {
                    btn.addEventListener('click', function() {
                        btnQuick.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        inputAmount.value = this.dataset.val;
                        checkSubmit();
                    });
                });
                
                inputAmount.addEventListener('input', function() {
                    btnQuick.forEach(b => b.classList.remove('active'));
                    checkSubmit();
                });

                // Countdown functions
                function formatTimerString(seconds) {
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                }

                function updateCountdownDisplay() {
                    const timeStr = formatTimerString(remainingSeconds);
                    if (countdownTimer) countdownTimer.textContent = timeStr;
                    if (modalCountdownTimer) modalCountdownTimer.textContent = timeStr;

                    const percentage = Math.max(0, (remainingSeconds / TOTAL_COUNTDOWN_SECONDS) * 100);
                    if (countdownProgressBar) countdownProgressBar.style.width = percentage + '%';

                    // Alert style when <= 2 minutes (120 seconds)
                    if (remainingSeconds <= 120 && remainingSeconds > 0) {
                        countdownBox?.classList.add('is-urgent');
                        modalTimerBadge?.classList.add('is-urgent');
                    }
                }

                function setExpiredState() {
                    isExpired = true;
                    if (countdownInterval) {
                        clearInterval(countdownInterval);
                        countdownInterval = null;
                    }

                    if (countdownTimer) countdownTimer.textContent = '00:00 (Hết hạn)';
                    if (modalCountdownTimer) modalCountdownTimer.textContent = '00:00 (Hết hạn)';

                    countdownBox?.classList.remove('is-urgent');
                    countdownBox?.classList.add('is-expired');
                    modalTimerBadge?.classList.remove('is-urgent');
                    modalTimerBadge?.classList.add('is-expired');

                    qrImgBox?.classList.add('is-expired');

                    if (btnDownloadQr) btnDownloadQr.disabled = true;
                    if (btnZoomQr) btnZoomQr.disabled = true;
                    if (btnDownloadModal) btnDownloadModal.disabled = true;

                    document.querySelectorAll('.copy-btn').forEach(btn => {
                        btn.disabled = true;
                    });

                    if (typeof FuiToast !== 'undefined') {
                        FuiToast.error('Mã QR đã hết hạn thanh toán (15 phút). Vui lòng tạo mã QR mới!');
                    }
                }

                function startCountdown() {
                    if (countdownInterval) {
                        clearInterval(countdownInterval);
                    }
                    isExpired = false;
                    remainingSeconds = TOTAL_COUNTDOWN_SECONDS;

                    countdownBox?.classList.remove('is-urgent', 'is-expired');
                    modalTimerBadge?.classList.remove('is-urgent', 'is-expired');
                    qrImgBox?.classList.remove('is-expired');

                    if (btnDownloadQr) btnDownloadQr.disabled = false;
                    if (btnZoomQr) btnZoomQr.disabled = false;
                    if (btnDownloadModal) btnDownloadModal.disabled = false;
                    document.querySelectorAll('.copy-btn').forEach(btn => {
                        btn.disabled = false;
                    });

                    updateCountdownDisplay();

                    countdownInterval = setInterval(function() {
                        remainingSeconds--;
                        if (remainingSeconds <= 0) {
                            remainingSeconds = 0;
                            updateCountdownDisplay();
                            setExpiredState();
                        } else {
                            updateCountdownDisplay();
                        }
                    }, 1000);
                }

                function resetInvoiceForm() {
                    if (countdownInterval) {
                        clearInterval(countdownInterval);
                        countdownInterval = null;
                    }
                    isExpired = false;
                    closeQrModal();
                    stepQr.style.display = 'none';
                    stepForm.style.display = 'block';
                }

                // Create Invoice Button Click
                btnSubmit.addEventListener('click', function() {
                    const amount = inputAmount.value;
                    const basePrefix = (selectedBank.prefix || '{{ config('sepay.prefix', 'Yuki') }}').trim().replace(/\d+$/, '') || 'Yuki';
                    // Pad ID to at least 3 digits to match SePay pattern (e.g. Yuki001)
                    const formattedId = String(userId).padStart(3, '0');
                    const content = basePrefix + formattedId;
                    const formattedAmount = new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
                    
                    document.getElementById('qr-bank').textContent = selectedBank.bank;
                    document.getElementById('qr-name').textContent = selectedBank.name;
                    document.getElementById('qr-acc').textContent = selectedBank.acc;
                    document.getElementById('qr-amount').textContent = formattedAmount;
                    document.getElementById('qr-content').textContent = content;
                    
                    document.getElementById('copy-acc').setAttribute('data-clipboard-text', selectedBank.acc);
                    document.getElementById('copy-amt').setAttribute('data-clipboard-text', amount);
                    document.getElementById('copy-content').setAttribute('data-clipboard-text', content);

                    if (modalQrAmount) modalQrAmount.textContent = formattedAmount;
                    if (modalQrContent) modalQrContent.textContent = content;
                    
                    const qrUrl = `https://img.vietqr.io/image/${selectedBank.bank}-${selectedBank.acc}-compact2.png?amount=${amount}&addInfo=${content}&accountName=${selectedBank.name}`;
                    qrImg.src = qrUrl;
                    
                    stepForm.style.display = 'none';
                    stepQr.style.display = 'block';

                    startCountdown();
                });
                
                if (btnBack) {
                    btnBack.addEventListener('click', resetInvoiceForm);
                }
                if (btnRenewQr) {
                    btnRenewQr.addEventListener('click', resetInvoiceForm);
                }

                // QR Zoom Modal Logic
                function openQrModal() {
                    if (isExpired) {
                        if (typeof FuiToast !== 'undefined') {
                            FuiToast.error('Mã QR đã hết hạn! Vui lòng tạo mã QR mới.');
                        }
                        return;
                    }
                    const qrSrc = qrImg.src;
                    if (qrSrc) {
                        qrModalImg.src = qrSrc;
                        qrModal.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    }
                }

                function closeQrModal() {
                    if (qrModal) {
                        qrModal.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }

                if (btnZoomQr) btnZoomQr.addEventListener('click', openQrModal);
                if (qrImgBox) {
                    qrImgBox.addEventListener('click', function(e) {
                        if (e.target.closest('#btn-renew-qr')) return;
                        if (!isExpired) openQrModal();
                    });
                }
                if (qrModalClose) qrModalClose.addEventListener('click', closeQrModal);
                if (btnCloseModalBottom) btnCloseModalBottom.addEventListener('click', closeQrModal);
                if (qrModal) {
                    qrModal.addEventListener('click', function(e) {
                        if (e.target === qrModal) closeQrModal();
                    });
                }
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && qrModal && qrModal.classList.contains('active')) {
                        closeQrModal();
                    }
                });

                // Download QR Image Function
                async function downloadQrImage() {
                    if (isExpired) {
                        if (typeof FuiToast !== 'undefined') {
                            FuiToast.error('Mã QR đã hết hạn! Vui lòng tạo mã mới để tải.');
                        }
                        return;
                    }
                    if (!qrImg || !qrImg.src) return;

                    const contentText = document.getElementById('qr-content')?.textContent?.trim() || 'payment';
                    const filename = `VietQR_${contentText}.png`;

                    const downloadButtons = [btnDownloadQr, btnDownloadModal].filter(Boolean);
                    downloadButtons.forEach(btn => {
                        btn.dataset.origHtml = btn.innerHTML;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
                        btn.disabled = true;
                    });

                    try {
                        // Attempt 1: Direct fetch blob (best for browsers with CORS)
                        const response = await fetch(qrImg.src, { mode: 'cors' });
                        if (!response.ok) throw new Error('Fetch failed');
                        const blob = await response.blob();
                        const blobUrl = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = blobUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(blobUrl);

                        if (typeof FuiToast !== 'undefined') {
                            FuiToast.success('Đã tải mã QR về máy thành công!');
                        }
                    } catch (err) {
                        // Attempt 2: Canvas draw fallback
                        try {
                            const canvasImg = new Image();
                            canvasImg.crossOrigin = 'anonymous';
                            canvasImg.onload = function() {
                                const canvas = document.createElement('canvas');
                                canvas.width = canvasImg.naturalWidth || canvasImg.width;
                                canvas.height = canvasImg.naturalHeight || canvasImg.height;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(canvasImg, 0, 0);
                                const dataUrl = canvas.toDataURL('image/png');
                                const a = document.createElement('a');
                                a.href = dataUrl;
                                a.download = filename;
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);

                                if (typeof FuiToast !== 'undefined') {
                                    FuiToast.success('Đã tải mã QR về máy thành công!');
                                }
                            };
                            canvasImg.onerror = function() {
                                // Final fallback: open image in a new tab so user can save it
                                window.open(qrImg.src, '_blank');
                            };
                            canvasImg.src = qrImg.src;
                        } catch (canvasErr) {
                            window.open(qrImg.src, '_blank');
                        }
                    } finally {
                        setTimeout(() => {
                            downloadButtons.forEach(btn => {
                                if (btn.dataset.origHtml) btn.innerHTML = btn.dataset.origHtml;
                                if (!isExpired) btn.disabled = false;
                            });
                        }, 1200);
                    }
                }

                if (btnDownloadQr) btnDownloadQr.addEventListener('click', downloadQrImage);
                if (btnDownloadModal) btnDownloadModal.addEventListener('click', downloadQrImage);

                // Close alert buttons
                const alertCloseButtons = document.querySelectorAll('.service__alert-close');
                alertCloseButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        this.closest('.service__alert').remove();
                    });
                });
            });
        </script>
    @endpush

@endsection
