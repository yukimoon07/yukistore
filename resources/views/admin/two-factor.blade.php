@extends('layouts.admin.app')

@section('title', 'Bảo mật hai lớp (2FA)')

@section('content')
<div class="twofa-page">
    <div class="pc-card twofa-card">
        <div class="twofa-head">
            <h4><i class="fas fa-shield-halved me-2"></i>Bảo mật hai lớp (2FA)</h4>
            @if ($enabled)
                <span class="badge twofa-badge-on">Đang bật</span>
            @else
                <span class="badge twofa-badge-off">Chưa bật</span>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('2fa:recovery-codes'))
            <div class="twofa-recovery">
                <strong>Mã khôi phục — chỉ hiển thị MỘT LẦN, hãy lưu lại ngay:</strong>
                <div class="twofa-recovery-grid">
                    @foreach (session('2fa:recovery-codes') as $rc)
                        <code>{{ $rc }}</code>
                    @endforeach
                </div>
                <small>Mỗi mã dùng được 1 lần duy nhất khi không có thiết bị Authenticator.</small>
            </div>
        @endif
        @php
            // Xóa khỏi session ngay sau khi hiển thị để không còn lần 2
            session()->forget('2fa:recovery-codes');
        @endphp

        @if (! $enabled)
            <p class="text-muted">Tăng cường bảo mật tài khoản quản trị: sau khi bật, mỗi lần đăng nhập sẽ yêu cầu thêm mã 6 số từ ứng dụng Authenticator (Google Authenticator, Microsoft Authenticator, Authy…).</p>

            <div class="twofa-setup">
                <div class="twofa-qr">
                    <ol class="small text-muted" style="padding-left:18px;">
                        <li>Mở ứng dụng Authenticator trên điện thoại (Google Authenticator, Microsoft Authenticator, Authy…)</li>
                        <li>Chọn <strong>+</strong> → <strong>Nhập mã khóa thiết lập</strong> (Enter a setup key)</li>
                        <li>Nhập tên tài khoản tùy ý và khóa bên dưới</li>
                        <li>Chọn "Time-based" rồi lưu</li>
                    </ol>
                    @if ($otpauthUri)
                        <p class="text-center">
                            <a href="{{ $otpauthUri }}" class="btn btn-outline-primary btn-sm">Mở bằng app trên máy này</a>
                        </p>
                    @endif
                </div>
                <div class="twofa-form">
                    <p class="small text-muted">Khóa thiết lập — nhập thủ công vào ứng dụng:</p>
                    <div class="twofa-secret"><code>{{ $secret }}</code></div>

                    <form method="POST" action="{{ route('admin.2fa.confirm') }}" class="mt-3">
                        @csrf
                        <label class="form-label">Nhập mã 6 số để xác nhận bật</label>
                        <input type="text" name="code" required inputmode="numeric" maxlength="6"
                               class="form-control @error('code') is-invalid @enderror"
                               placeholder="000000" style="letter-spacing:6px;text-align:center;max-width:200px">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <button type="submit" class="btn btn-primary mt-3">Bật bảo mật hai lớp</button>
                    </form>
                </div>
            </div>
        @else
            <p>Bạn đã bật 2FA. Mỗi lần đăng nhập vào khu vực quản trị sẽ cần mã từ ứng dụng Authenticator.</p>

            <div class="d-flex gap-2 flex-wrap">
                <form method="POST" action="{{ route('admin.2fa.recovery') }}" onsubmit="return confirm('Tạo lại 8 mã khôi phục mới? Các mã cũ sẽ hết hiệu lực.')">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">Tạo lại mã khôi phục</button>
                </form>

                <button type="button" class="btn btn-outline-danger" onclick="document.getElementById('twofa-disable').style.display='block'">Tắt 2FA</button>
            </div>

            <div id="twofa-disable" style="display:none" class="twofa-disable-box">
                <h6>Xác nhận tắt 2FA</h6>
                <form method="POST" action="{{ route('admin.2fa.disable') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" name="password" required class="form-control" autocomplete="current-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mã OTP hiện tại</label>
                        <input type="text" name="code" required class="form-control @error('code') is-invalid @enderror" maxlength="6" style="letter-spacing:6px;max-width:200px">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-danger">Xác nhận tắt</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('twofa-disable').style.display='none'">Hủy</button>
                </form>
            </div>
        @endif
    </div>
</div>

<style>
.twofa-page{max-width:760px;margin:0 auto}
.twofa-card{padding:24px}
.twofa-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.twofa-head h4{margin:0}
.twofa-badge-on{background:rgba(34,197,94,.15);color:#16a34a;padding:6px 12px;border-radius:20px}
.twofa-badge-off{background:rgba(245,158,11,.15);color:#b45309;padding:6px 12px;border-radius:20px}
.twofa-setup{display:flex;gap:28px;flex-wrap:wrap;margin-top:16px}
.twofa-qr{flex:0 0 220px}
.twofa-form{flex:1;min-width:260px}
.twofa-secret{background:rgba(128,128,128,.08);padding:10px 12px;border-radius:8px;word-break:break-all}
.twofa-recovery{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.35);border-radius:10px;padding:16px;margin-bottom:16px}
.twofa-recovery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px;margin:12px 0}
.twofa-recovery-grid code{background:rgba(0,0,0,.06);padding:6px 10px;border-radius:6px;text-align:center}
.twofa-disable-box{margin-top:16px;border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:16px;background:rgba(239,68,68,.05)}
</style>
@endsection
