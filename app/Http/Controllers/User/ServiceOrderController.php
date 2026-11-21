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
            'package_ids' => 'nullable|array|max:3',
            'package_ids.*' => 'exists:service_packages,id',
            'package_id' => 'nullable|exists:service_packages,id',
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

        // Bắt đầu transaction TRƯỚC khi đọc số dư để chống double submission
        DB::beginTransaction();

        try {
            // Khóa bản ghi user để chặn 2 request cùng lúc đọc số dư cũ
            $user = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            // RÀNG BUỘC: Chỉ được đặt 1 đơn trong lúc cày, sau khi admin xác nhận là cày xong thì mới được đặt tiếp
            $hasActiveOrder = ServiceHistory::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'processing'])
                ->exists();

            if ($hasActiveOrder) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Bạn đang có đơn cày thuê đang được xử lý. Vui lòng chờ hoàn tất đơn hiện tại trước khi đặt đơn mới!')
                    ->withInput();
            }

            // Xử lý danh sách gói đã chọn (hỗ trợ tối đa 3 gói cày thuê trên 1 đơn)
            $packageIds = $request->input('package_ids');
            if (empty($packageIds) && $request->filled('package_id')) {
                $packageIds = [$request->input('package_id')];
            }

            if (!is_array($packageIds) || count($packageIds) === 0) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Vui lòng chọn ít nhất 1 gói cày thuê.')
                    ->withInput();
            }

            if (count($packageIds) > 3) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Chỉ được phép chọn tối đa 3 mục cày thuê cùng 1 lúc trên 1 đơn.')
                    ->withInput();
            }

            // Xác nhận các gói thuộc đúng service và đang hoạt động
            $packages = ServicePackage::whereIn('id', $packageIds)
                ->where('game_service_id', $request->input('service_id'))
                ->where('active', true)
                ->get();

            if ($packages->count() !== count($packageIds)) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Có gói dịch vụ không hợp lệ hoặc không thuộc dịch vụ này.')
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

            // Tính tổng tiền các gói
            $totalOriginalPrice = 0;
            $packageNames = [];
            foreach ($packages as $pkg) {
                if ($pkg->price === null || (int) $pkg->price < 0) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', "Gói '{$pkg->name}' chưa có giá hợp lệ. Vui lòng liên hệ quản trị viên.")
                        ->withInput();
                }
                $totalOriginalPrice += (int) $pkg->price;
                $packageNames[] = $pkg->name;
            }

            $packageNamesStr = implode(' · ', $packageNames);

            // Xử lý mã giảm giá
            $finalPrice = $totalOriginalPrice;
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
                    ->with('error', 'Số dư tài khoản không đủ để thanh toán đơn cày thuê này. Vui lòng nạp thêm tiền!')
                    ->withInput();
            }

            // Ghi chú chi tiết gói & tài khoản
            $userNote = trim((string) $request->input('note'));
            $fullNote = count($packages) > 1 ? "Gói cày: [{$packageNamesStr}]" : "Gói: {$packageNamesStr}";
            if ($userNote !== '') {
                $fullNote .= " | Ghi chú: {$userNote}";
            }

            // Tạo lịch sử dịch vụ
            $serviceHistoryData = [
                'user_id' => $user->id,
                'game_service_id' => $service->id,
                'service_package_id' => $packages->first()->id,
                'game_account' => $request->input('game_account'),
                'game_password' => $request->input('game_password'),
                'server' => 0, // Đã xóa mục Máy chủ theo yêu cầu của bạn
                'amount' => count($packages),
                'price' => $finalPrice,
                'discount_code' => $discountCode ? $discountCode->code : null,
                'discount_amount' => $discountAmount,
                'status' => 'pending',
                'admin_note' => $fullNote,
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('service_histories', 'package_names')) {
                $serviceHistoryData['package_names'] = $packageNamesStr;
            }

            $serviceHistory = ServiceHistory::create($serviceHistoryData);

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
                'description' => 'Thuê ' . $service->name . ' (' . $packageNamesStr . ') #' . $serviceHistory->id .
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
