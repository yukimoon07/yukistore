<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OrderReview;
use App\Models\ServiceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_order_id' => 'required|exists:service_histories,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ], [
            'service_order_id.required' => 'Đơn hàng không hợp lệ.',
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.min' => 'Đánh giá tối thiểu 1 sao.',
            'rating.max' => 'Đánh giá tối đa 5 sao.',
            'comment.required' => 'Vui lòng nhập nội dung nhận xét.',
            'comment.min' => 'Nội dung nhận xét tối thiểu 5 ký tự.',
        ]);

        $order = ServiceHistory::where('id', $request->service_order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể đánh giá đơn hàng đã hoàn thành.',
            ], 400);
        }

        // Kiểm tra xem đã đánh giá chưa
        $review = OrderReview::where('service_order_id', $order->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($review) {
            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            $msg = 'Đã cập nhật đánh giá của bạn thành công!';
        } else {
            OrderReview::create([
                'user_id' => Auth::id(),
                'service_order_id' => $order->id,
                'order_type' => 'service',
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'approved',
            ]);
            $msg = 'Cảm ơn bạn đã gửi đánh giá!';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
