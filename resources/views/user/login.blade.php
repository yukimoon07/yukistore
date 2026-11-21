@extends('layouts.user.app')

@section('title', 'Sign in - ' . (config('app.name') ?: 'ShopGame'))

@push('css')
<style>
    .auth-page {
        min-height: calc(100vh - 64px - 280px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 48px 20px;
    }
    .auth-card {
        background: #0f1626;
        width: 100%;
        max-width: 420px;
        border-radius: 16px;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.6);
        padding: 32px 28px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .auth-header {
        text-align: center;
        margin-bottom: 24px;
    }
    .auth-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #f8fafc;
        margin: 0 0 6px 0;
        letter-spacing: -0.02em;
    }
    .auth-subtitle {
        color: #94a3b8;
        font-size: 0.88rem;
        margin: 0;
    }
    .federated-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 20px;
    }
    .social-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 10px 16px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.05);
        color: #f1f5f9;
        font-weight: 600;
        text-decoration: none;
        font-size: 0.88rem;
        cursor: pointer;
        transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease;
    }
    .social-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.22);
        color: #ffffff;
    }
    .social-btn .iconify {
        font-size: 1.15rem;
    }
    .form-group {
        margin-bottom: 18px;
    }
    .form-label {
        display: block;
        font-weight: 600;
        font-size: 0.84rem;
        margin-bottom: 6px;
        color: #cbd5e1;
    }
    .form-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: #080c14;
        color: #f8fafc;
        font-size: 0.9rem;
        transition: border-color 150ms ease, box-shadow 150ms ease;
        outline: none;
        box-sizing: border-box;
    }
    .form-input:focus {
        border-color: var(--yuki-gold, #E8B33C);
        box-shadow: 0 0 0 3px rgba(232, 179, 60, 0.25);
    }
    .form-error {
        color: #f87171;
        font-size: 0.8rem;
        margin-top: 5px;
        display: block;
    }
    .auth-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #E8B33C, #C2922A);
        color: #0b0f19;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: transform 150ms ease, filter 150ms ease, box-shadow 150ms ease;
        margin-top: 6px;
        box-shadow: 0 4px 14px rgba(232, 179, 60, 0.25);
    }
    .auth-btn:hover {
        filter: brightness(1.08);
        box-shadow: 0 6px 18px rgba(232, 179, 60, 0.35);
    }
    .auth-btn:active {
        transform: scale(0.99);
    }
    .auth-links {
        text-align: center;
        margin-top: 24px;
        font-size: 0.86rem;
        color: #94a3b8;
    }
    .auth-links a {
        color: var(--yuki-gold, #E8B33C);
        text-decoration: none;
        font-weight: 600;
        margin-left: 4px;
    }
    .auth-links a:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Sign in</h1>
            <p class="auth-subtitle">Đăng nhập tài khoản của bạn để tiếp tục</p>
        </div>

        @if (session('error'))
            <div style="background: rgba(239, 68, 68, 0.15); color: #f87171; padding: 12px 14px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 18px; border: 1px solid rgba(239, 68, 68, 0.3); display: flex; align-items: center; gap: 8px;">
                <span class="iconify" data-icon="ant-design:exclamation-circle-outlined" style="font-size: 16px;"></span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- 1. Federated sign-in buttons ABOVE sign-in method divider --}}
        <div class="federated-buttons">
            <a href="{{ route('auth.google') }}" class="social-btn" aria-label="Đăng nhập với Google">
                <span class="iconify" data-icon="flat-color-icons:google"></span>
                <span>Đăng nhập với Google</span>
            </a>
            <a href="{{ route('auth.facebook') }}" class="social-btn" aria-label="Đăng nhập với Facebook">
                <span class="iconify" data-icon="logos:facebook" style="font-size: 1.15rem;"></span>
                <span>Đăng nhập với Facebook</span>
            </a>
        </div>

        {{-- 2. Sign-in method divider: thin rule with "or" in the middle --}}
        <div class="auth-divider-or" role="separator" aria-label="Hoặc đăng nhập bằng tài khoản">Hoặc bằng tài khoản</div>

        @php
            $loginCaptcha = \App\Helpers\CaptchaHelper::generate('login');
        @endphp

        {{-- 3. Native Form Semantics --}}
        <form method="POST" action="{{ route('login') }}" autocomplete="on" novalidate>
            @csrf

            {{-- Labeled email/username field (<input autocomplete="username">) --}}
            <div class="form-group">
                <label for="username" class="form-label">Tên đăng nhập hoặc Email</label>
                <input id="username" 
                       type="text" 
                       class="form-input" 
                       name="username" 
                       autocomplete="username" 
                       value="{{ old('username') }}" 
                       required 
                       autofocus 
                       placeholder="Nhập username hoặc email...">
                @error('username')
                    <span class="form-error" role="alert">{{ $message }}</span>
                @enderror
            </div>

            {{-- Labeled password field (<input type="password" autocomplete="current-password">) with visibility toggle --}}
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label for="password" class="form-label" style="margin-bottom: 0;">Mật khẩu</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size: 0.82rem; color: var(--yuki-gold, #E8B33C); text-decoration: none;">Quên mật khẩu?</a>
                    @endif
                </div>

                <div class="password-input-wrapper">
                    <input id="password" 
                           type="password" 
                           class="form-input" 
                           name="password" 
                           autocomplete="current-password" 
                           required 
                           placeholder="••••••••">
                    <button type="button" 
                            class="password-toggle-btn" 
                            id="passwordToggleBtn"
                            aria-label="Show password" 
                            onclick="togglePasswordVisibility('password', this)">
                        <span class="iconify eye-show" data-icon="ant-design:eye-outlined"></span>
                        <span class="iconify eye-hide" data-icon="ant-design:eye-invisible-outlined" style="display: none;"></span>
                    </button>
                </div>
                @error('password')
                    <span class="form-error" role="alert">{{ $message }}</span>
                @enderror
            </div>

            {{-- Captcha --}}
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label for="captcha" class="form-label" style="margin-bottom: 0;">Mã bảo vệ (Captcha)</label>
                    <span style="background: rgba(232, 179, 60, 0.15); color: var(--yuki-gold, #E8B33C); padding: 2px 10px; border-radius: 6px; font-weight: 700; font-size: 0.95rem; border: 1px solid rgba(232, 179, 60, 0.3);">
                        {{ $loginCaptcha['question'] }}
                    </span>
                </div>
                <input id="captcha" 
                       type="number" 
                       class="form-input" 
                       name="captcha" 
                       required 
                       placeholder="Nhập kết quả phép tính trên...">
                @error('captcha')
                    <span class="form-error" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem;">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="cursor: pointer; accent-color: var(--yuki-gold, #E8B33C); width: 16px; height: 16px;">
                    <label for="remember" style="cursor: pointer; color: #94a3b8; user-select: none;">Ghi nhớ đăng nhập</label>
                </div>
            </div>

            {{-- <button type="submit"> labeled "Sign in" so Enter submits --}}
            <button type="submit" class="auth-btn" id="submitLoginBtn">
                Sign in
            </button>
        </form>

        <div class="auth-links">
            Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, btn) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        
        var eyeShow = btn.querySelector('.eye-show');
        var eyeHide = btn.querySelector('.eye-hide');
        if (eyeShow && eyeHide) {
            eyeShow.style.display = isPassword ? 'none' : 'inline';
            eyeHide.style.display = isPassword ? 'inline' : 'none';
        }
    }
</script>
@endsection
