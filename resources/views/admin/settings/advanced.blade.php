@extends('layouts.admin.app')
@section('title', $title ?? 'Cài đặt nâng cao')

@section('content')
    <div>
        <div class="page-header">
            <div class="page-block mb-3">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0"><i class="ti ti-adjustments-horizontal me-2"></i>{{ $title }}</h2>
                            <p class="text-muted">Chế độ bảo trì, giới hạn giao dịch và tham số vận hành</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.settings.advanced.update') }}" method="POST">
            @csrf

            {{-- Chế độ bảo trì --}}
            <div class="card border {{ $configs['site_maintenance'] ? 'border-danger' : 'border-dashed' }} shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom-0 pt-3">
                    <h5 class="mb-0 fw-bold {{ $configs['site_maintenance'] ? 'text-danger' : '' }}">
                        <i class="ti ti-tool me-2"></i>Chế độ bảo trì
                    </h5>
                </div>
                <div class="card-body">
                    @if($configs['site_maintenance'])
                        <div class="alert alert-danger py-2">
                            <i class="ti ti-alert-triangle me-1"></i>
                            <strong>Website đang ở chế độ bảo trì!</strong> Khách truy cập sẽ thấy trang 503.
                        </div>
                    @endif
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="site_maintenance"
                            name="site_maintenance" value="1" {{ $configs['site_maintenance'] ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="site_maintenance">
                            Bật chế độ bảo trì (tắt toàn bộ website, admin vẫn xem được qua secret)
                        </label>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Thông điệp bảo trì hiển thị cho khách</label>
                        <textarea name="site_maintenance_message" class="form-control" rows="2"
                            placeholder="Website đang bảo trì, vui lòng quay lại sau ít phút.">{{ old('site_maintenance_message', $configs['site_maintenance_message']) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Giới hạn rút tiền --}}
            <div class="card border border-dashed shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom-0 pt-3">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-cash-banknote me-2"></i>Giới hạn rút tiền</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Số tiền rút tối thiểu (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="min_withdraw_money" min="0" step="1000"
                                class="form-control @error('min_withdraw_money') is-invalid @enderror"
                                value="{{ old('min_withdraw_money', $configs['min_withdraw_money']) }}">
                            @error('min_withdraw_money')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mặc định 100.000đ. Áp dụng cho form rút tiền của người dùng.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số tiền rút tối đa (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="max_withdraw_money" min="0" step="1000"
                                class="form-control @error('max_withdraw_money') is-invalid @enderror"
                                value="{{ old('max_withdraw_money', $configs['max_withdraw_money']) }}">
                            @error('max_withdraw_money')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mặc định 10.000.000đ.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tham số hệ thống --}}
            <div class="card border border-dashed shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom-0 pt-3">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-chart-pie me-2"></i>Tham số hệ thống</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Biên lợi nhuận ước tính (0 - 1) <span class="text-danger">*</span></label>
                            <input type="number" name="estimated_profit_margin" min="0" max="1" step="0.05"
                                class="form-control @error('estimated_profit_margin') is-invalid @enderror"
                                value="{{ old('estimated_profit_margin', $configs['estimated_profit_margin']) }}">
                            @error('estimated_profit_margin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Dùng để tính "Lợi nhuận" trên Dashboard. 0.4 = 40%.</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="welcome_modal"
                                    name="welcome_modal" value="1" {{ $configs['welcome_modal'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="welcome_modal">
                                    Hiển thị popup thông báo khi khách vào website
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i> Lưu cài đặt
                </button>
                <a href="{{ route('admin.index') }}" class="btn btn-light-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
@endsection
