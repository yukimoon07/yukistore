@extends('layouts.admin.app')

@section('title', $title)

@section('content')
<div>
    <div class="page-header mb-4">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-1">{{ $title }}</h2>
                        <p class="text-muted mb-0">Bộ chọn đa mục (Multi-Select Listbox) & Bộ điều phối hai bảng (Ant Transfer) chuẩn Accessibility & Keyboard navigation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Component 1: Multi-Select Listbox -->
    <div class="card mb-4 shadow-sm border">
        <div class="card-header bg-auto-subtle d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-1 fw-bold text-primary">
                    <i class="ti ti-list-check me-1"></i> Multi-Select Listbox (role="listbox" & aria-multiselectable="true")
                </h5>
                <small class="text-muted">Trigger thu gọn hiện số lượng đã chọn, danh sách tìm kiếm luôn mở khi chọn nhiều mục, gắn chip tháo rời, tách biệt tiêu điểm bàn phím.</small>
            </div>
            <span class="badge bg-light-primary text-primary px-3 py-2 fw-semibold">WAI-ARIA Pattern</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-4 mb-lg-0">
                    <form onsubmit="event.preventDefault();">
                        <x-multi-select-listbox 
                            id="adminDemoMultiSelect"
                            name="selected_categories"
                            label="Chọn các nhóm sản phẩm & danh mục game quản lý:"
                            placeholder="Nhấn để mở danh sách..."
                            :selected="['lq', 'blox', 'genshin']"
                            :options="[
                                ['value' => 'lq', 'label' => 'Liên Quân Mobile (Account Vip)'],
                                ['value' => 'blox', 'label' => 'Blox Fruits (Roblox Premium)'],
                                ['value' => 'genshin', 'label' => 'Genshin Impact (Ar 55-60)'],
                                ['value' => 'fco', 'label' => 'FC Online (Đội hình tỷ BP)'],
                                ['value' => 'fcm', 'label' => 'FC Mobile (OVR cao)'],
                                ['value' => 'val', 'label' => 'Valorant (Full Skin Prime)'],
                                ['value' => 'ark', 'label' => 'Arknights: Endfield'],
                                ['value' => 'random_200k', 'label' => 'Thử Vận May 200K'],
                                ['value' => 'random_100k', 'label' => 'Thử Vận May 100K'],
                                ['value' => 'tui_mu_99k', 'label' => 'Túi Mù May Mắn 99K'],
                            ]"
                        />
                    </form>

                    <div class="alert alert-info py-2 px-3 small mt-2">
                        <i class="ti ti-info-circle me-1"></i>
                        <strong>Thao tác bàn phím:</strong> Dùng <kbd>↓</kbd> / <kbd>↑</kbd> để di chuyển tiêu điểm (không đổi trạng thái), phím <kbd>Space</kbd> hoặc <kbd>Enter</kbd> để chọn/bỏ chọn, phím <kbd>Esc</kbd> để đóng.
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="p-3 bg-light rounded border h-100">
                        <h6 class="fw-bold mb-2 text-dark"><i class="ti ti-code me-1"></i> Trạng thái tương tác thời gian thực:</h6>
                        <div id="mslLiveLog" class="small font-monospace p-2 bg-dark text-light rounded" style="min-height: 180px; max-height: 220px; overflow-y: auto;">
                            Đang lắng nghe sự kiện msl:change...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Component 2: Ant Transfer (Two-pane variant) -->
    <div class="card mb-4 shadow-sm border">
        <div class="card-header bg-auto-subtle d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-1 fw-bold text-success">
                    <i class="ti ti-arrows-left-right me-1"></i> Ant Transfer (Two-Pane Selector)
                </h5>
                <small class="text-muted">Hai bảng "Available" & "Selected", lọc tìm kiếm độc lập, các nút chuyển chỉ kích hoạt khi có mục hợp lệ được tích chọn.</small>
            </div>
            <span class="badge bg-light-success text-success px-3 py-2 fw-semibold">Ant Design Style</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8 col-12 mb-4 mb-lg-0">
                    <label class="form-label fw-bold mb-2">Phân bổ dịch vụ & phân quyền CTV cày thuê (Ant Transfer):</label>
                    <x-ant-transfer 
                        id="adminDemoTransfer"
                        name="assigned_services"
                        :titles="['Available (Có sẵn)', 'Selected (Đã gán)']"
                        :targetKeys="['srv_1', 'srv_3', 'srv_5']"
                        :dataSource="[
                            ['key' => 'srv_1', 'title' => 'Cày Rank Liên Quân Cao Thủ', 'description' => 'Moba - 150K'],
                            ['key' => 'srv_2', 'title' => 'Cày Level Blox Fruits Max 2550', 'description' => 'Roblox - 90K'],
                            ['key' => 'srv_3', 'title' => 'Farm Rương & Nhiệm vụ Genshin', 'description' => 'Gacha - 200K'],
                            ['key' => 'srv_4', 'title' => 'Cày Thuê Rank Valorant Radiant', 'description' => 'FPS - 350K'],
                            ['key' => 'srv_5', 'title' => 'Đua Top Xếp Hạng FC Online', 'description' => 'Sport - 120K'],
                            ['key' => 'srv_6', 'title' => 'Cày Cúp FC Mobile Mùa Mới', 'description' => 'Sport - 80K'],
                            ['key' => 'srv_7', 'title' => 'Săn Trùm Thế Giới Mirage V4', 'description' => 'Roblox - 180K'],
                            ['key' => 'srv_8', 'title' => 'Kéo Điểm Tháp Arknights', 'description' => 'RPG - 160K'],
                        ]"
                    />
                </div>

                <div class="col-lg-4 col-12">
                    <div class="p-3 bg-light rounded border h-100">
                        <h6 class="fw-bold mb-2 text-dark"><i class="ti ti-database me-1"></i> Dữ liệu `targetKeys` xuất form:</h6>
                        <div id="transferLiveLog" class="small font-monospace p-2 bg-dark text-light rounded" style="min-height: 180px; max-height: 240px; overflow-y: auto;">
                            targetKeys: ["srv_1", "srv_3", "srv_5"]
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('msl:change', function (e) {
        var log = document.getElementById('mslLiveLog');
        if (log && e.detail) {
            log.innerHTML = '<strong>[Event msl:change]</strong><br>' +
                'Selected count: ' + e.detail.values.length + '<br>' +
                'Keys: ' + JSON.stringify(e.detail.values, null, 2) + '<br>' +
                'Items:<br>' + e.detail.selected.map(function(s) { return '• ' + s.label + ' (' + s.value + ')'; }).join('<br>');
        }
    });

    document.addEventListener('transfer:change', function (e) {
        var log = document.getElementById('transferLiveLog');
        if (log && e.detail) {
            log.innerHTML = '<strong>[Event transfer:change]</strong><br>' +
                'targetKeys count: ' + e.detail.targetCount + '<br>' +
                'Available count: ' + e.detail.sourceCount + '<br>' +
                'targetKeys:<br>' + JSON.stringify(e.detail.targetKeys, null, 2);
        }
    });
</script>
@endsection
