@extends('layouts.user.app')

@section('title', 'Đăng ký tài khoản')

@push('css')
<style>
    .auth-page {
        min-height: calc(100vh - 64px - 300px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background-color: transparent;
    }
    .auth-card {
        background: #fff;
        width: 100%;
        max-width: 440px;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 32px;
        border: 1px solid #e5e7eb;
    }
    .auth-header {
        text-align: center;
        margin-bottom: 24px;
    }
    .auth-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 8px 0;
    }
    .auth-subtitle {
        color: #666;
        font-size: 0.9rem;
        margin: 0;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        display: block;
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 6px;
        color: #333;
    }
    .form-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #d9d9d9;
        font-size: 0.9rem;
        transition: all 0.2s;
        outline: none;
    }
    .form-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px var(--primary-glow);
    }
    .form-error {
        color: #dc2626;
        font-size: 0.8rem;
        margin-top: 4px;
        display: block;
    }
    .auth-btn {
        width: 100%;
        padding: 12px;
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 12px;
    }
    .auth-btn:hover {
        background: var(--primary-dark);
    }
    .auth-links {
        text-align: center;
        margin-top: 20px;
        font-size: 0.85rem;
    }
    .auth-links a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }
    .auth-links a:hover {
        text-decoration: underline;
    }
    .social-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 24px 0;
        color: #999;
        font-size: 0.8rem;
    }
    .social-divider::before, .social-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e5e7eb;
    }
    .social-divider:not(:empty)::before {
        margin-right: .25em;
    }
    .social-divider:not(:empty)::after {
        margin-left: .25em;
    }
    .social-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #333;
        font-weight: 500;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
        margin-bottom: 10px;
    }
    .social-btn:hover {
        background: #f9f9f9;
        border-color: #d9d9d9;
    }
    
    /* Dark Mode */
    [data-theme="dark"] .auth-card {
        background: #171717;
        border-color: #2a2a2a;
    }
    [data-theme="dark"] .auth-title { color: #fff; }
    [data-theme="dark"] .auth-subtitle { color: #999; }
    [data-theme="dark"] .form-label { color: #ccc; }
    [data-theme="dark"] .form-input {
        background: #262626;
        border-color: #404040;
        color: #fff;
    }
    [data-theme="dark"] .social-divider::before, [data-theme="dark"] .social-divider::after {
        border-color: #2a2a2a;
    }
    [data-theme="dark"] .social-btn {
        background: #262626;
        border-color: #404040;
        color: #ccc;
    }
    [data-theme="dark"] .social-btn:hover {
        background: #333;
    }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Đăng Ký Tài Khoản</h1>
            <p class="auth-subtitle">Tạo tài khoản để sử dụng dịch vụ dễ dàng hơn</p>
        </div>

        <x-alert-error />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="username" class="form-label">Tên tài khoản</label>
                <input id="username" type="text" class="form-input" name="username" value="{{ old('username') }}" required autofocus
                       minlength="3" maxlength="24" pattern="[a-zA-Z][a-zA-Z0-9_]+"
                       title="3-24 ký tự: chữ, số, gạch dưới; bắt đầu bằng chữ cái"
                       placeholder="VD: tuanori">
                <small style="color:#999;font-size:0.78rem;">3-24 ký tự, chữ/số/gạch dưới, bắt đầu bằng chữ</small>
                @error('username')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-input" name="email" value="{{ old('email') }}" required placeholder="VD: example@gmail.com">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mật khẩu</label>
                <input id="password" type="password" class="form-input" name="password" required minlength="8"
                       autocomplete="new-password" placeholder="Tối thiểu 8 ký tự"
                       oninput="updateStrength(this.value)">
                <div id="pw-strength-bar" style="height:4px;border-radius:2px;background:#e5e7eb;margin-top:6px;overflow:hidden;">
                    <div id="pw-strength-fill" style="height:100%;width:0;background:#ef4444;transition:width .2s,background .2s;"></div>
                </div>
                <small id="pw-strength-text" style="color:#999;font-size:0.78rem;">Tối thiểu 8 ký tự — nên có chữ hoa, chữ thường và số</small>
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm" class="form-label">Xác nhận mật khẩu</label>
                <input id="password-confirm" type="password" class="form-input" name="password_confirmation" required
                       autocomplete="new-password" placeholder="Nhập lại mật khẩu"
                       oninput="checkMatch(this.value, document.getElementById('password').value)">
                <small id="pw-match-text" style="display:none;color:#16a34a;font-size:0.78rem;">✓ Mật khẩu khớp</small>
            </div>

            <button type="submit" class="auth-btn">
                Đăng Ký Ngay
            </button>
        </form>

<script>
function updateStrength(v) {
    var score = 0;
    if (v.length >= 8) score++;
    if (v.length >= 12) score++;
    if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
    if (/\d/.test(v)) score++;
    if (/[^a-zA-Z0-9]/.test(v)) score++;
    var fill = document.getElementById('pw-strength-fill');
    var txt = document.getElementById('pw-strength-text');
    var levels = [
        {w:'0%',   c:'#ef4444', t:'Tối thiểu 8 ký tự — nên có chữ hoa, chữ thường và số'},
        {w:'20%',  c:'#ef4444', t:'Yếu'},
        {w:'40%',  c:'#f59e0b', t:'Trung bình'},
        {w:'60%',  c:'#f59e0b', t:'Khá'},
        {w:'80%',  c:'#22c55e', t:'Mạnh'},
        {w:'100%', c:'#16a34a', t:'Rất mạnh'}
    ];
    var lv = levels[Math.min(score, 5)];
    fill.style.width = lv.w;
    fill.style.background = lv.c;
    txt.textContent = v ? lv.t : 'Tối thiểu 8 ký tự — nên có chữ hoa, chữ thường và số';
}
function checkMatch(confirmVal, pwVal) {
    var el = document.getElementById('pw-match-text');
    if (confirmVal && confirmVal === pwVal) { el.style.display = 'block'; el.style.color = '#16a34a'; el.textContent = '✓ Mật khẩu khớp'; }
    else if (confirmVal) { el.style.display = 'block'; el.style.color = '#ef4444'; el.textContent = '✗ Chưa khớp'; }
    else { el.style.display = 'none'; }
}
</script>

        @if (config_get('login_social.google.active', false) || config_get('login_social.facebook.active', false))
            <div class="social-divider">Hoặc đăng ký bằng</div>
            
            @if (config_get('login_social.google.active', false))
                <a href="{{ route('auth.google') }}" class="social-btn">
                    <span class="iconify" data-icon="flat-color-icons:google" style="font-size:1.2rem;"></span>
                    Google
                </a>
            @endif
            
            @if (config_get('login_social.facebook.active', false))
                <a href="{{ route('auth.facebook') }}" class="social-btn">
                    <span class="iconify" data-icon="logos:facebook" style="font-size:1.2rem;"></span>
                    Facebook
                </a>
            @endif
        @endif

        <div class="auth-links">
            Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
        </div>
    </div>
</div>
@endsection
