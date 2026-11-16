<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\BankDeposit;
use App\Models\MoneyWithdrawalHistory;
use App\Models\ServiceHistory;

/**
 * Hàng đợi duyệt nhanh từ dashboard:
 * - Duyệt / từ chối rút tiền ( MoneyWithdrawalHistory )
 * - Duyệt / từ chối đơn dịch vụ ( ServiceHistory )
 * Dùng chung logic với MoneyWithdrawalController + ServiceOrder flow,
 * nhưng trả JSON để dashboard cập nhật không cần reload cả trang.
 */
class QuickActionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Số liệu hàng đợi cho dashboard (badge).
     */
    public static function pendingCounts(): array
    {
        try {
            return [
                'withdrawals' => MoneyWithdrawalHistory::where('status', 'processing')->count(),
                'services'    => ServiceHistory::where('status', 'pending')->count(),
                'deposits'    => BankDeposit::where('status', 'pending')->count(),
            ];
        } catch (\Throwable $e) {
            return ['withdrawals' => 0, 'services' => 0, 'deposits' => 0];
        }
    }

    public function approveWithdrawal(MoneyWithdrawalHistory $withdrawal)
    {
        if ($withdrawal->status !== 'processing') {
            return response()->json(['success' => false, 'message' => 'Yêu cầu này đã được xử lý.'], 422);
        }

        $withdrawal->update(['status' => 'success']);

        AdminActivityLog::record('withdrawal.approve', $withdrawal, ['status' => 'processing'], ['status' => 'success']);

        return response()->json(['success' => true, 'message' => 'Đã duyệt yêu cầu rút tiền #' . $withdrawal->id]);
    }

    public function rejectWithdrawal(MoneyWithdrawalHistory $withdrawal)
    {
        if ($withdrawal->status !== 'processing') {
            return response()->json(['success' => false, 'message' => 'Yêu cầu này đã được xử lý.'], 422);
        }

        try {
            \DB::beginTransaction();

            $withdrawal->update([
                'status' => 'error',
                'admin_note' => 'Từ chối từ Dashboard',
            ]);

            // Hoàn tiền cho người dùng (khóa dòng user để tránh race)
            $user = \App\Models\User::where('id', $withdrawal->user_id)->lockForUpdate()->first();
            if ($user) {
                $before = $user->balance;
                $after = $before + $withdrawal->amount;
                $user->update(['balance' => $after]);

                \App\Models\MoneyTransaction::create([
                    'user_id' => $withdrawal->user_id,
                    'type' => 'refund',
                    'amount' => $withdrawal->amount,
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'description' => 'Hoàn tiền do từ chối rút tiền #' . $withdrawal->id,
                ]);
            }

            AdminActivityLog::record('withdrawal.reject', $withdrawal, ['status' => 'processing'], ['status' => 'error']);

            \DB::commit();

            return response()->json(['success' => true, 'message' => 'Đã từ chối và hoàn tiền cho yêu cầu #' . $withdrawal->id]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Lỗi từ chối rút tiền (quick): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại.'], 500);
        }
    }

    public function completeService(ServiceHistory $service)
    {
        if (!in_array($service->status, ['pending', 'processing'], true)) {
            return response()->json(['success' => false, 'message' => 'Đơn này đã được xử lý.'], 422);
        }

        $old = $service->status;
        $service->update(['status' => 'completed']);

        AdminActivityLog::record('service.complete', $service, ['status' => $old], ['status' => 'completed']);

        return response()->json(['success' => true, 'message' => 'Đã hoàn thành đơn dịch vụ #' . $service->id]);
    }
}
