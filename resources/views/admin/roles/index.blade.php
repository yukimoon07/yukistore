@extends('layouts.admin.app')

@section('title', $title)

@section('content')
    <div>
        <div>
            <div class="page-header">
                <div class="page-block mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">{{ $title }}</h2>
                                <p class="text-muted">Nâng cấp hoặc hạ quyền người dùng thành viên → Dev → Admin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Bảng danh sách admin / dev -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Danh sách tài khoản quản trị</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">ID</th>
                                    <th>Tên tài khoản</th>
                                    <th>Email</th>
                                    <th class="text-center" style="width: 150px;">Vai trò</th>
                                    <th class="text-center" style="width: 150px;">Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $user)
                                    <tr>
                                        <td class="text-center">{{ $user->id }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $user->username }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="badge bg-light-secondary text-secondary">(tôi)</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $user->email }}</td>
                                        <td class="text-center">
                                            @php
                                                $badgeClass = 'bg-light-secondary text-secondary';
                                                $label = 'Người dùng';
                                                if ($user->role === 'admin') { $badgeClass = 'bg-light-danger text-danger'; $label = 'Quản trị viên'; }
                                                elseif ($user->role === 'dev') { $badgeClass = 'bg-light-primary text-primary'; $label = 'Dev'; }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-2 py-1">{{ $label }}</span>
                                        </td>
                                        <td class="text-center text-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Không có tài khoản quản trị nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form cấp quyền -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Cấp quyền cho người dùng</h5>
                    <form action="{{ route('admin.roles.assign') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label">Người dùng</label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">-- Chọn tài khoản --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->username }} ({{ $u->email }}) — {{ $u->role === 'admin' ? 'Quản trị viên' : ($u->role === 'dev' ? 'Dev' : 'Thành viên') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label">Vai trò mới</label>
                                <select name="role" class="form-select" required>
                                    <option value="member">Thành viên (Member)</option>
                                    <option value="dev">Dev (quản trị viên cấp thấp)</option>
                                    <option value="admin">Quản trị viên (Admin)</option>
                                </select>
                                <small class="form-text text-muted">Dev có thể vào admin panel nhưng không thể vào Cài đặt hệ thống, cấp quyền, xem 2FA của người khác và Nhật ký hoạt động.</small>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Cập nhật quyền</button>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Giải thích vai trò -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Mô tả vai trò</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="fw-semibold">Admin (Quản trị viên cấp cao)</div>
                            <span class="badge bg-danger">Mọi quyền</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="fw-semibold">Dev (quản trị viên cấp thấp)</div>
                            <span class="badge bg-primary">Dùng được trang quản trị dữ liệu, bị chặn cài đặt hệ thống, cấp quyền, 2FA của người khác và Nhật ký hoạt động</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="fw-semibold">Member (Thành viên)</div>
                            <span class="badge bg-secondary">Không vào admin panel</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
