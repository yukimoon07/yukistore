@extends('layouts.admin.auth')

@section('title', 'Xác thực hai lớp')

@section('content')
<div class="admin-login-wrap">
    <div class="admin-login-card">
        <div class="admin-login-header">
            <div class="admin-login-icon" style="background:rgba(34,197,94,.1);color:#22c55e;">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h1>Xác thực hai lớp</h1>
            <p>Nhập mã 6 số từ ứng dụng Authenticator trên điện thoại của bạn</p>
        </div>

        <form method="POST" action="{{ route('admin.2fa.challenge.verify') }}">
            @csrf
            <div class="admin-login-field">
                <label for="code">Mã xác thực</label>
                <input id="code" type="text" name="code" required autofocus
                       autocomplete="one-time-code" inputmode="numeric"
                       pattern="[0-9A-Za-z\-]*" maxlength="11"
                       placeholder="000000 hoặc mã khôi phục"
                       style="text-align:center;letter-spacing:6px;font-size:1.15rem">
                @error('code')<span class="admin-login-error">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="admin-login-btn">Xác nhận</button>
        </form>

        <div class="admin-login-foot">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="linklike">Dùng tài khoản khác</button>
            </form>
        </div>
    </div>
</div>

<style>
.admin-login-wrap{min-height:70vh;display:flex;align-items:center;justify-content:center;padding:40px 16px}
.admin-login-card{width:100%;max-width:400px;background:var(--card-bg,#fff);border:1px solid rgba(128,128,128,.18);
    border-radius:14px;padding:32px;box-shadow:0 8px 30px rgba(0,0,0,.08)}
.admin-login-header{text-align:center;margin-bottom:24px}
.admin-login-icon{width:52px;height:52px;margin:0 auto 14px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;font-size:1.4rem}
.admin-login-header h1{font-size:1.3rem;font-weight:700;margin:0 0 6px}
.admin-login-header p{color:#8a94a6;font-size:.86rem;margin:0}
.admin-login-field{margin-bottom:16px}
.admin-login-field label{display:block;font-size:.83rem;font-weight:600;margin-bottom:6px}
.admin-login-field input{width:100%;padding:10px 12px;border:1px solid rgba(128,128,128,.3);border-radius:8px;
    font-size:.92rem;background:transparent;color:inherit}
.admin-login-field input:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15)}
.admin-login-error{display:block;color:#ef4444;font-size:.8rem;margin-top:5px}
.admin-login-btn{width:100%;padding:11px;background:#2563eb;color:#fff;border:none;border-radius:8px;
    font-weight:600;font-size:.95rem;cursor:pointer;transition:background .15s}
.admin-login-btn:hover{background:#1d4ed8}
.admin-login-foot{text-align:center;margin-top:18px}
.linklike{background:none;border:none;color:#8a94a6;font-size:.83rem;cursor:pointer;text-decoration:underline}
.linklike:hover{color:#2563eb}
</style>
@endsection
