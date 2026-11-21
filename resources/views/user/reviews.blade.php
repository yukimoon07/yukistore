@extends('layouts.user.app')
@section('title', 'Nhận xét của khách hàng')
@section('content')

<div class="container" style="margin-top: 30px; margin-bottom: 40px;">
    <div class="section-header" style="text-align:center; margin-bottom: 25px;">
        <h2 class="section-title" style="display:inline-block; font-size: 1.5rem; justify-content: center; width: 100%;">
            <span class="iconify" data-icon="ant-design:star-filled" style="color:#faad14; font-size: 1.6rem; vertical-align: middle;"></span> 
            Nhận xét của khách hàng khi sử dụng dịch vụ tại shop 
            <span class="iconify" data-icon="ant-design:star-filled" style="color:#faad14; font-size: 1.6rem; vertical-align: middle;"></span>
        </h2>
    </div>

    <!-- Interactive Experience Rating Card (Prompt #016) -->
    <div class="user-experience-rating-card" style="margin: 0 auto 36px; max-width: 460px; padding: 24px; border-radius: 20px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 16px 36px rgba(0, 0, 0, 0.25); text-align: center;">
        <x-star-rating-u016 
            id="reviewsCustomerRating"
            question="Bạn cảm thấy thế nào về trải nghiệm tại cửa hàng?"
            :words="['Chạm vào sao để đánh giá', 'Rất tệ', 'Tạm được', 'Hài lòng', 'Tuyệt vời', 'Xuất sắc']"
            :colors="['var(--ux-ink-3)', '#E5484D', '#F07A3E', '#E8A317', '#2FB37A', 'var(--yuki-gold, #E8B33C)']"
            inputName="customer_experience_rating"
        />
        <div id="ratingSuccessNotice" style="display: none; margin-top: 12px; font-size: 0.9rem; color: #10b981; font-weight: 700;">
            <i class="fa-solid fa-circle-check"></i> Cảm ơn bạn đã gửi đánh giá! Trải nghiệm của bạn giúp shop ngày càng hoàn thiện.
        </div>
    </div>

    <div style="margin-bottom: 15px; font-weight: 600; color: #4b5563;">
        @if(isset($purchases) && $purchases->total() > 0)
            Có {{ $purchases->total() }} lượt mua được xác thực
        @else
            Chưa có lượt mua nào
        @endif
    </div>

    @if(isset($purchases) && $purchases->total() > 0)
    <div class="review-grid">
        @foreach($purchases as $purchase)
            <div class="review-card">
                <div class="review-header">
                    <div class="review-info">
                        <div class="review-name">{{ $purchase['name'] }}</div>
                        <div class="review-meta">
                            <span class="review-id">đã mua nick #{{ $purchase['id'] }}</span>
                            @if (!empty($purchase['date']))
                                <span class="review-id">· {{ $purchase['date'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="review-text">Giao dịch thành công</div>
            </div>
        @endforeach
    </div>

    @if($purchases->hasPages())
        <div style="margin-top: 30px;">
            {{ $purchases->links('user.pagination.custom') }}
        </div>
    @endif
    @else
    <div style="padding: 40px 20px; text-align: center; color: #6b7280; background: rgba(255,255,255,0.04); border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
        <p>Chưa có đánh giá nào được xác thực. Hãy quay lại sau khi có giao dịch đầu tiên.</p>
        <p><a href="{{ route('home') }}" style="color: #38bdf8;">← Quay về trang chủ</a></p>
    </div>
    @endif
</div>

<style>
    .review-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 1024px) {
        .review-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .review-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .review-grid { grid-template-columns: 1fr; }
    }
    .review-card {
        background: #fff;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .review-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    [data-theme="dark"] .review-card {
        background: #1f1f1f;
        border-color: #333;
    }
    .review-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .review-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .review-info {
        flex: 1;
    }
    .review-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1f2937;
        margin-bottom: 4px;
    }
    [data-theme="dark"] .review-name {
        color: #f3f4f6;
    }
    .review-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
    }
    .review-stars {
        color: #faad14;
        display: flex;
        gap: 2px;
    }
    .review-id {
        color: #9ca3af;
    }
    .review-text {
        font-size: 0.9rem;
        color: #4b5563;
        line-height: 1.5;
    }
    [data-theme="dark"] .review-text {
        color: #9ca3af;
    }

    /* Fake Pagination styles since standard laravel pagination might need bootstrap */
    .custom-pagination .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 5px;
    }
    .custom-pagination .page-link {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #374151;
        background-color: #fff;
        border: none;
        font-weight: 500;
    }
    .custom-pagination .page-item.active .page-link {
        z-index: 3;
        color: red;
        background-color: transparent;
        font-weight: bold;
    }
    .custom-pagination .page-item:not(.active) .page-link:hover {
        color: red;
        background-color: #f3f4f6;
        border-radius: 4px;
    }
    [data-theme="dark"] .custom-pagination .page-link {
        background-color: #1f1f1f;
        color: #d1d5db;
    }
    [data-theme="dark"] .custom-pagination .page-item.active .page-link {
        color: #ef4444;
    }
    [data-theme="dark"] .custom-pagination .page-item:not(.active) .page-link:hover {
        background-color: #374151;
        color: #ef4444;
    }
</style>
<script>
    document.addEventListener('u016:rate', function(e) {
        if (e.target && e.target.id === 'reviewsCustomerRating') {
            var notice = document.getElementById('ratingSuccessNotice');
            if (notice) {
                notice.style.display = 'block';
            }
        }
    });
</script>
@endsection
