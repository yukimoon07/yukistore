@extends('layouts.admin.app')

@section('content')
<div class="page-header mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">{{ $title }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.missions.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.missions.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label required">Tên nhiệm vụ</label>
                    <input type="text" name="title" class="form-control" placeholder="Ví dụ: Vượt link nhận xu #1" required value="{{ old('title') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Loại dịch vụ</label>
                    <select name="service_type" class="form-select">
                        <option value="link1s">Link1s</option>
                        <option value="link4m">Link4m</option>
                        <option value="link123">Link123</option>
                        <option value="dilink">Dilink</option>
                        <option value="web1s">Web1s</option>
                        <option value="review_map">Review Google Maps</option>
                        <option value="custom" selected>Tùy chỉnh (Custom)</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label required">Link đích (URL nhiệm vụ / Link rút gọn)</label>
                    <input type="url" name="target_url" class="form-control" placeholder="https://..." required value="{{ old('target_url') }}">
                    <small class="form-hint">Người dùng sẽ được chuyển hướng tới liên kết này sau khi bấm Làm ngay.</small>
                </div>

                <div class="col-md-12">
                    <label class="form-label">API Token (nếu có đối tác như Review Map)</label>
                    <input type="text" name="api_token" class="form-control" placeholder="Token API tích hợp..." value="{{ old('api_token') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Tiền thưởng tối thiểu (VNĐ)</label>
                    <input type="number" name="reward_min" class="form-control" value="{{ old('reward_min', 100) }}" required min="1">
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Tiền thưởng tối đa (VNĐ)</label>
                    <input type="number" name="reward_max" class="form-control" value="{{ old('reward_max', 250) }}" required min="1">
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Thời gian giãn cách (Giây)</label>
                    <input type="number" name="cooldown_seconds" class="form-control" value="{{ old('cooldown_seconds', 120) }}" required min="0">
                    <small class="form-hint">Thời gian đếm ngược chống spam làm lại.</small>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Mô tả ngắn</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Mô tả tóm tắt nhiệm vụ...">{{ old('description') }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Các bước hướng dẫn chi tiết (HTML)</label>
                    <textarea name="guide_steps" class="form-control" rows="4" placeholder="Để trống nếu muốn dùng hướng dẫn mặc định 3 bước...">{{ old('guide_steps') }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                        <span class="form-check-label">Kích hoạt hiển thị cho thành viên làm</span>
                    </label>
                </div>
            </div>

            <div class="mt-4 text-end">
                <a href="{{ route('admin.missions.index') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-check me-1"></i> Lưu nhiệm vụ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
