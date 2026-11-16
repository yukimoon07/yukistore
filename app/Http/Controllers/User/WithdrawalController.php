<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MoneyWithdrawalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Show the withdrawal form.
     */
    public function create()
    {
        $title = "Rút tiền";
        $minWithdraw = (int) config_get('min_withdraw_money', 100000);
        $maxWithdraw = (int) config_get('max_withdraw_money', 10000000);

        return view('user.profile.withdraw', compact('title', 'minWithdraw', 'maxWithdraw'));
    }

    /**
     * Store a new withdrawal request.
     */
    public function store(Request $request)
    {
        $minWithdraw = (int) config_get('min_withdraw_money', 100000);
        $maxWithdraw = (int) config_get('max_withdraw_money', 10000000);

        $request->validate([
            'amount' => "required|integer|min:{$minWithdraw}|max:{$maxWithdraw}",
            'user_note' => 'nullable|string|max:255',
        ], [
            'amount.min' => 'Số tiền rút tối thiểu là ' . number_format($minWithdraw) . 'đ.',
            'amount.max' => 'Số tiền rút tối đa là ' . number_format($maxWithdraw) . 'đ mỗi lần.',
        ]);

        $user = auth()->user();

        try {
            DB::beginTransaction();

            // Khóa bản ghi user và kiểm tra số dư MỚI NHẤT bên trong transaction
            // để chặn 2 yêu cầu rút đồng thời vượt quá số dư.
            $lockedUser = \App\Models\User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            if ($lockedUser->balance < $request->amount) {
                DB::rollBack();
                return back()->with('error', 'Số dư không đủ để thực hiện giao dịch.');
            }

            // Tạo yêu cầu rút tiền
            $withdrawal = MoneyWithdrawalHistory::create([
                'user_id' => $lockedUser->id,
                'amount' => $request->amount,
                'user_note' => $request->user_note,
                'status' => 'processing',
            ]);

            // Trừ tiền từ tài khoản người dùng
            $lockedUser->update(['balance' => $lockedUser->balance - $request->amount]);

            DB::commit();

            return redirect()->route('profile.withdraw.history')
                ->with('success', 'Yêu cầu rút tiền đã được gửi thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Lỗi tạo yêu cầu rút tiền: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $request->amount,
            ]);
            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
        }
    }

    /**
     * Display the withdrawal history.
     */
    public function history()
    {
        $title = "Lịch sử rút tiền";
        $withdrawals = MoneyWithdrawalHistory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.profile.withdrawal-history', compact('withdrawals', 'title'));
    }
}