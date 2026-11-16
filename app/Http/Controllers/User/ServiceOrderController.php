<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\MoneyTransaction;
use App\Models\ServiceHistory;
use App\Models\ServicePackage;
use App\Models\GameService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ServiceOrderController extends Controller
{
    public function processOrder(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:game_services,id',
            'package_id' => 'required|exists:service_packages,id',
            'server' => 'nullable|string|max:50',
            'game_account' => 'required|string|max:50',
            'game_password' => 'required|string|max:50',
            'note' => 'nullable|string|max:500',
            'giftcode' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::findOrFail(auth()->id());

        // Bắt đầu transaction TRƯỚC khi đọc số dư để chống double submission:
        // mọi luồng thanh toán (đọc số dư, kiểm tra gói, trừ tiền, ghi lịch sử) nằm trong một transaction.
        DB::beginTransaction();

        try {
            // Khóa bản ghi user để chặn 2 request cùng lúc đọc số dư cũ
            $user = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            // Lấy package ĐỒNG THỜI xác nhận package thuộc đúng service được yêu cầu
            // và đang hoạt động (không chỉ kiểm tra tồn tại độc lập).
            $package = ServicePackage::where('id', $request->input('package_id'))
                ->where('game_service_id', $request->input('service_id'))
                ->where('active', true)
                ->first();

            if (!$package) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Gói dịch vụ không tồn tại hoặc không thuộc dịch vụ này.')
                    ->withInput();
            }

            // Xác nhận service tồn tại và đang bật
            $service = GameService::where('id', $request->input('service_id'))
                ->where('active', true)
                ->first();

            if (!$service) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Dịch vụ không tồn tại hoặc đã bị tắt.')
                    ->withInput();
            }

            // Giá phải hợp lệ (> 0). Không cho đặt gói chưa được thiết lập giá.
            if ($package->price === null || (int) $package->price < 0) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Gói dịch vụ này chưa có giá hợp lệ. Vui lòng liên hệ quản trị viên.')
                    ->withInput();
            }

            // Xử lý mã giảm giá
            $finalPrice = (int) $package->price;
            $discountAmount = 0;
            $discountCode = null;

            if ($request->filled('giftcode')) {
                $discountCode = DiscountCode::where('code', $request->giftcode)
                    ->where('is_active', '1')
                    ->first();

                if ($discountCode) {
                    if ($discountCode->discount_type === 'percentage') {
                        $discountAmount = ($finalPrice * $discountCode->discount_value) / 100;
                        if ($discountCode->max_discount_value && $discountAmount > $discountCode->max_discount_value) {
                            $discountAmount = $discountCode->max_discount_value;
                        }
                    } else {
                        $discountAmount = $discountCode->discount_value;
                    }

                    $finalPrice = max(0, $finalPrice - (int) $discountAmount);
                }
            }

            // Đọc số dư MỚI NHẤT trong transaction (đã khóa bản ghi user)
            if ($user->balance < $finalPrice) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Số dư tài khoản không đủ để thanh toán dịch vụ này.')
                    ->withInput();
            }

            // Tạo lịch sử dịch vụ
            $serviceHistory = ServiceHistory::create([
                'user_id' => $user->id,
                'game_service_id' => $service->id,
                'service_package_id' => $package->id,
                'game_account' => $request->input('game_account'),
                'game_password' => $request->input('game_password'),
                'server' => $request->input('server') ?: 'Mặc định',
                'note' => $request->input('note'),
                'price' => $finalPrice,
                'status' => 'pending',
            ]);

            // Trừ tiền tài khoản
            $balanceBefore = $user->balance;
            $balanceAfter = $balanceBefore - $finalPrice;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['balance' => $balanceAfter]);

            // Lịch sử biến động số dư
            MoneyTransaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount' => -$finalPrice,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => 'Thuê ' . $service->name . ' #' . $serviceHistory->id .
                    ($discountAmount > 0 ? ' (Giảm giá: ' . number_format($discountAmount) . 'đ)' : ''),
                'reference_id' => $serviceHistory->id
            ]);

            // Ghi nhận sử dụng mã giảm giá
            if ($discountCode) {
                DB::table('discount_codes')
                    ->where('id', $discountCode->id)
                    ->increment('usage_count');

                DB::table('discount_code_usages')->insert([
                    'discount_code_id' => $discountCode->id,
                    'user_id' => $user->id,
                    'context' => 'service',
                    'item_id' => $serviceHistory->id,
                    'original_price' => $package->price,
                    'discounted_price' => $finalPrice,
                    'discount_amount' => $discountAmount,
                    'used_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return back()->with('success', 'Đặt dịch vụ thành công. Chúng tôi sẽ xử lý trong thời gian sớm nhất.');
        } catch (\Throwable $e) {
            DB::rollBack();

            // Ghi log đầy đủ phía server — KHÔNG trả chi tiết kỹ thuật (SQL, bảng, cấu hình)
            // ra cho khách hàng.
            Log::error('Lỗi đặt dịch vụ: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'service_id' => $request->input('service_id'),
                'package_id' => $request->input('package_id'),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi đặt dịch vụ. Vui lòng thử lại sau ít phút.')
                ->withInput();
        }
    }
}
