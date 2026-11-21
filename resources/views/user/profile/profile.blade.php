@extends('layouts.user.app')

@section('title', $title)

@section('content')
    <section class="profile-section">
        <div class="container">
            <div class="profile-container">
                <div class="profile-header">
                    <h1 class="profile-title"><i class="fa-solid fa-user-circle me-2"></i> THÔNG TIN TÀI KHOẢN</h1>
                </div>

                <div class="profile-content">
                    @include('layouts.user.sidebar')

                    <div class="profile-main">
                        <div class="profile-info-card">
                            <div class="info-header">
                                <div class="balance-info">
                                    <span class="balance-label"><i class="fa-solid fa-wallet me-2"></i> THÔNG TIN TÀI
                                        KHOẢN</span>
                                </div>
                            </div>

                            <div class="info-content">
                                <style>
                                    .info-content { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
                                    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px dashed #e2e8f0; }
                                    .info-row:last-child { border-bottom: none; padding-bottom: 0; }
                                    .info-row:first-child { padding-top: 0; }
                                    .info-label { color: #64748b; font-weight: 500; font-size: 0.95rem; display: flex; align-items: center; }
                                    .info-label i { width: 24px; color: #94a3b8; }
                                    .info-value { color: #0f172a; font-weight: 600; font-size: 1rem; text-align: right; display: flex; align-items: center; justify-content: flex-end; gap: 12px; }
                                    .info-value--highlight { color: #ef4444; font-size: 1.1rem; }
                                    .change-password-link { background: #fef2f2; color: #ef4444; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; text-decoration: none; transition: 0.2s; border: 1px solid #fecaca; }
                                    .change-password-link:hover { background: #fee2e2; color: #dc2626; }
                                    
                                    /* Dark mode */
                                    [data-theme="dark"] .info-content { background: #171717; border: 1px solid #2a2a2a; }
                                    [data-theme="dark"] .info-row { border-bottom-color: #333; }
                                    [data-theme="dark"] .info-label { color: #9ca3af; }
                                    [data-theme="dark"] .info-value { color: #f8fafc; }
                                    [data-theme="dark"] .change-password-link { background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); }
                                    [data-theme="dark"] .change-password-link:hover { background: rgba(239, 68, 68, 0.2); }
                                    
                                    @media (max-width: 576px) {
                                        .info-row { flex-direction: column; align-items: flex-start; gap: 8px; }
                                        .info-value { width: 100%; justify-content: space-between; flex-direction: row-reverse; }
                                    }
                                </style>
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-id-card me-2"></i> ID tài khoản
                                    </div>
                                    <div class="info-value">{{ $user->id }}</div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-user me-2"></i> Tên đăng nhập
                                    </div>
                                    <div class="info-value">{{ $user->username }}</div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-address-card me-2"></i> Họ và tên
                                    </div>
                                    <div class="info-value">{{ $user->name ?: 'Chưa cập nhật' }}</div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-phone me-2"></i> Số điện thoại
                                    </div>
                                    <div class="info-value">{{ $user->phone ?: 'Chưa cập nhật' }}</div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-envelope me-2"></i> Email
                                    </div>
                                    <div class="info-value">{{ $user->email }}</div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-key me-2"></i> Mật khẩu
                                    </div>
                                    <div class="info-value">
                                        ********
                                        <a href="{{ route('profile.change-password') }}" class="change-password-link">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Đổi mật khẩu
                                        </a>
                                    </div>
                                </div>


                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-wallet me-2"></i> Số dư
                                    </div>
                                    <div class="info-value info-value--highlight">
                                        {{ number_format($user->balance) }} VND
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-money-bill-trend-up me-2"></i> Tổng nạp
                                    </div>
                                        {{ number_format($user->total_deposited) }} VND
                                    </div>
                                </div>

                             

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-gem me-2"></i> Vật Phẩm
                                    </div>
                                    <div class="info-value">
                                        {{ number_format($user->gem) }}
                                        <a href="{{ route('profile.withdraw-gem') }}" class="change-password-link">
                                            <i class="fa-solid fa-gem me-1"></i> Rút Vật Phẩm
                                        </a>
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fa-solid fa-calendar-check me-2"></i> Ngày tạo
                                    </div>
                                    <div class="info-value">
                                        {{ $user->created_at->format('H:i d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form cập nhật hồ sơ --}}
                        <div class="profile-info-card mt-4">
                            <div class="info-header">
                                <div class="balance-info">
                                    <span class="balance-label"><i class="fa-solid fa-user-pen me-2"></i> CẬP NHẬT THÔNG TIN HỒ SƠ</span>
                                </div>
                            </div>
                            <div class="info-content" style="margin-top: 10px;">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px; margin-bottom: 20px;">
                                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger" style="border-radius: 8px; margin-bottom: 20px;">
                                        <ul class="mb-0 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Họ và tên</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" placeholder="Nhập họ và tên...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Số điện thoại</label>
                                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="Nhập số điện thoại...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" placeholder="Nhập địa chỉ email...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold" style="font-size: 0.9rem; color: #475569;">Ảnh đại diện (Avatar)</label>
                                            <input type="file" name="avatar" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-12 mt-3 text-end">
                                            <button type="submit" class="btn btn-primary px-4 py-2" style="background: var(--yuki-gold, #E8B33C); border-color: var(--yuki-gold, #E8B33C); color: #000; font-weight: 700; border-radius: 8px;">
                                                <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
