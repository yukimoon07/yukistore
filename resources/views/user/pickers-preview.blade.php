@extends('layouts.user.app')

@section('title', 'Multi-select Listbox & Ant Transfer')

@section('content')
<div class="container" style="padding-top: 32px; padding-bottom: 64px;">
    <!-- Breadcrumb -->
    <nav class="gaming-breadcrumb" aria-label="Breadcrumb" style="margin-bottom: 24px;">
        <a href="/"><i class="fa-solid fa-house"></i> Trang Chủ</a>
        <span class="gaming-breadcrumb-sep">/</span>
        <span class="gaming-breadcrumb-current">Multi-select Listbox & Ant Transfer</span>
    </nav>

    <!-- Header & Guide -->
    <div style="text-align: center; max-width: 720px; margin: 0 auto 36px;">
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px; background: rgba(232, 179, 60, 0.15); border: 1px solid rgba(232, 179, 60, 0.35); color: var(--yuki-gold, #E8B33C); font-weight: 700; font-size: 0.85rem; margin-bottom: 12px;">
            <i class="fa-solid fa-layer-group"></i> Advanced Selection Components
        </div>
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 2rem; font-weight: 800; color: #f8fafc; margin: 0 0 10px;">
            Multi-Select Listbox & Two-Pane Ant Transfer
        </h1>
        <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6; margin: 0;">
            Bộ chọn đa mục với <code>role="listbox"</code> và <code>aria-multiselectable="true"</code>, trigger hiển thị số lượng chọn, ô tìm kiếm giữ mở khi chọn nhiều mục, chip có thể xóa và tách biệt tiêu điểm bàn phím; cùng phiên bản Ant Transfer hai bảng Available & Selected.
        </p>
    </div>

    <!-- Theme Switcher -->
    <div style="display: flex; justify-content: center; align-items: center; gap: 16px; margin-bottom: 32px;">
        <button type="button" onclick="setPickersTheme('dark')" id="btnDark" style="padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-moon"></i> Dark Theme
        </button>
        <button type="button" onclick="setPickersTheme('light')" id="btnLight" style="padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #fff; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-sun"></i> Light Theme
        </button>
    </div>

    <!-- Showcase Container -->
    <div id="pickersContainer" style="display: flex; flex-direction: column; gap: 40px; padding: 36px 28px; border-radius: 24px; background: rgba(11, 15, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 20px 48px rgba(0,0,0,0.5); transition: background 0.3s ease;">
        
        <!-- Component 1: Multi-Select Listbox -->
        <div style="max-width: 680px; width: 100%; margin: 0 auto;">
            <div style="margin-bottom: 16px;">
                <h3 style="font-size: 1.25rem; font-weight: 750; color: #f8fafc; margin: 0 0 6px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-square-check" style="color: var(--yuki-gold, #E8B33C);"></i>
                    1. Multi-Select with role="listbox" & aria-multiselectable="true"
                </h3>
                <p style="font-size: 0.88rem; color: #94a3b8; margin: 0;">
                    Trigger thu gọn hiển thị số mục đã chọn, danh sách tùy chọn tìm kiếm giữ mở khi chọn liên tiếp, từng mục được hiển thị dạng chip có thể xóa (removable chip), phím điều hướng bàn phím tách biệt với thao tác chọn.
                </p>
            </div>

            <x-multi-select-listbox 
                id="userDemoMultiSelect"
                name="user_selected_games"
                label="Chọn các tựa game bạn muốn nhận thông báo ưu đãi:"
                placeholder="Chọn một hoặc nhiều tựa game..."
                :selected="['lq', 'blox', 'genshin']"
                :options="[
                    ['value' => 'lq', 'label' => 'Liên Quân Mobile'],
                    ['value' => 'blox', 'label' => 'Blox Fruits (Roblox)'],
                    ['value' => 'genshin', 'label' => 'Genshin Impact'],
                    ['value' => 'fco', 'label' => 'FC Online'],
                    ['value' => 'fcm', 'label' => 'FC Mobile'],
                    ['value' => 'val', 'label' => 'Valorant'],
                    ['value' => 'ark', 'label' => 'Arknights: Endfield'],
                    ['value' => 'lucky', 'label' => 'Vòng Quay May Mắn'],
                    ['value' => 'blindbox', 'label' => 'Túi Mù Bí Ẩn'],
                ]"
            />
        </div>

        <div style="border-top: 1px dashed rgba(255,255,255,0.1); width: 100%;"></div>

        <!-- Component 2: Ant Transfer -->
        <div style="max-width: 760px; width: 100%; margin: 0 auto;">
            <div style="margin-bottom: 16px;">
                <h3 style="font-size: 1.25rem; font-weight: 750; color: #f8fafc; margin: 0 0 6px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrows-split-up-and-left" style="color: #10b981;"></i>
                    2. Two-Pane Ant Transfer (Available & Selected)
                </h3>
                <p style="font-size: 0.88rem; color: #94a3b8; margin: 0;">
                    Hai bảng titled <strong>Available</strong> & <strong>Selected</strong>, biến <code>targetKeys</code> quản lý các mục đã chọn, các nút chuyển <strong>&gt;</strong> và <strong>&lt;</strong> bị vô hiệu hóa (disabled) khi không có mục hợp lệ nào được tích chọn.
                </p>
            </div>

            <x-ant-transfer 
                id="userDemoTransfer"
                name="user_favorite_services"
                :titles="['Available (Có sẵn)', 'Selected (Đã chọn)']"
                :targetKeys="['srv_1', 'srv_2']"
                :dataSource="[
                    ['key' => 'srv_1', 'title' => 'Cày Rank Cao Thủ Liên Quân', 'description' => 'Moba'],
                    ['key' => 'srv_2', 'title' => 'Cày Level Max Blox Fruits', 'description' => 'Roblox'],
                    ['key' => 'srv_3', 'title' => 'Farm Rương Khám Phá Genshin', 'description' => 'Gacha'],
                    ['key' => 'srv_4', 'title' => 'Cày Rank Valorant Immortal', 'description' => 'FPS'],
                    ['key' => 'srv_5', 'title' => 'Đá Xếp Hạng FC Online', 'description' => 'Sport'],
                    ['key' => 'srv_6', 'title' => 'Up Cúp FC Mobile Mùa Mới', 'description' => 'Sport'],
                    ['key' => 'srv_7', 'title' => 'Săn Trùm Thế Giới Blox Fruits', 'description' => 'Roblox'],
                ]"
            />
        </div>

    </div>

    <!-- Live Event Log -->
    <div style="max-width: 680px; margin: 32px auto 0; padding: 20px 24px; border-radius: 14px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); font-size: 0.88rem; color: #94a3b8; line-height: 1.6;">
        <div style="font-weight: 700; color: #f8fafc; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Checklist kiểm tra:
        </div>
        <ul style="margin: 0; padding-left: 20px;">
            <li><strong>Multi-Select Listbox:</strong> Thuộc tính <code>role="listbox"</code> và <code>aria-multiselectable="true"</code>. Nhấp vào các dòng tùy chọn liên tục mà danh sách không bị đóng. Mỗi mục đã chọn được render thành removable chip có nút xóa riêng.</li>
            <li><strong>Tách biệt tiêu điểm bàn phím:</strong> Dùng phím <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">↓</kbd> / <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">↑</kbd> để duyệt qua các option (tiêu điểm hiển thị outline vàng), nhấn <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">Space</kbd> hoặc <kbd style="background: rgba(255,255,255,0.1); padding: 1px 6px; border-radius: 4px;">Enter</kbd> để bật/tắt <code>aria-selected</code>.</li>
            <li><strong>Ant Transfer:</strong> Hai bảng có tiêu đề Available và Selected. Nút điều hướng <code>&gt;</code> và <code>&lt;</code> tự động bị vô hiệu hóa (disabled) khi không có ô checkbox nào được chọn ở bảng nguồn tương ứng.</li>
        </ul>
    </div>
</div>

<script>
    function setPickersTheme(theme) {
        var container = document.getElementById('pickersContainer');
        if (theme === 'light') {
            container.style.background = '#e5e7eb';
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            container.style.background = 'rgba(11, 15, 25, 0.6)';
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    }
</script>
@endsection
