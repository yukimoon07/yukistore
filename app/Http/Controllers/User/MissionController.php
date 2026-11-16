<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\MissionGiftcode;
use App\Models\MissionHistory;
use App\Models\MoneyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MissionController extends Controller
{
    /**
     * Danh sách tất cả nhiệm vụ kiếm tiền đang hoạt động
     */
    public function index()
    {
        $missions = Mission::where('status', true)->orderBy('id', 'asc')->get();

        $stats = [
            'total_completed' => 0,
            'total_earned' => 0,
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $stats['total_completed'] = MissionHistory::where('user_id', $user->id)->count();
            $stats['total_earned'] = MissionHistory::where('user_id', $user->id)->sum('reward_amount');
        }

        return view('user.mission.index', compact('missions', 'stats'));
    }

    /**
     * Chi tiết hướng dẫn nhiệm vụ
     */
    public function show($id)
    {
        $mission = Mission::where('status', true)->where(function($q) use ($id) {
            $q->where('id', $id)->orWhere('slug', $id);
        })->firstOrFail();

        // Kiểm tra cooldown theo IP hoặc user
        $lastAction = null;
        if (Auth::check()) {
            $lastAction = MissionHistory::where('user_id', Auth::id())
                ->where('mission_id', $mission->id)
                ->latest()
                ->first();
        } else {
            $lastAction = MissionHistory::where('ip_address', request()->ip())
                ->where('mission_id', $mission->id)
                ->latest()
                ->first();
        }

        $cooldownRemaining = 0;
        if ($lastAction && $lastAction->created_at) {
            $diff = now()->diffInSeconds($lastAction->created_at);
            if ($diff < $mission->cooldown_seconds) {
                $cooldownRemaining = $mission->cooldown_seconds - $diff;
            }
        }

        return view('user.mission.show', compact('mission', 'cooldownRemaining'));
    }

    /**
     * Bắt đầu nhiệm vụ: Tự sinh Giftcode và điều hướng qua link đích
     */
    public function start(Request $request, $id)
    {
        $mission = Mission::where('status', true)->findOrFail($id);

        // Sinh mã code ngẫu nhiên 8 ký tự (chữ in hoa + số)
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Random số tiền thưởng trong khoảng min - max
        $rewardAmount = rand((int)$mission->reward_min, (int)$mission->reward_max);

        // Tạo giftcode nhiệm vụ trong CSDL
        $giftcode = MissionGiftcode::create([
            'mission_id' => $mission->id,
            'code' => $code,
            'reward_amount' => $rewardAmount,
            'usages_left' => 1,
            'status' => 'active',
            'expires_at' => now()->addHours(6),
        ]);

        $targetUrl = $mission->target_url;

        // Nếu là dịch vụ có API token riêng (như Review Map hoặc Shortlink có truyền code)
        if (!empty($mission->api_token) && str_contains($targetUrl, 'dichvureviewmap.com')) {
            $redirectUrl = "https://dichvureviewmap.com/note/api.php?token={$mission->api_token}&title=" . urlencode("Mã Giftcode") . "&noidung={$code}";
            return redirect()->away($redirectUrl);
        }

        if (empty($targetUrl)) {
            $targetUrl = route('missions.giftcode');
        }

        return redirect()->away($targetUrl);
    }

    /**
     * Trang nhập mã Giftcode đổi thưởng
     */
    public function giftcodeView()
    {
        $recentHistories = collect();
        if (Auth::check()) {
            $recentHistories = MissionHistory::with('mission')
                ->where('user_id', Auth::id())
                ->latest()
                ->take(10)
                ->get();
        }

        return view('user.mission.giftcode', compact('recentHistories'));
    }

    /**
     * Xử lý xác thực và nạp Giftcode cộng tiền vào tài khoản
     */
    public function redeemGiftcode(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Vui lòng đăng nhập để thực hiện đổi mã Giftcode!'
            ], 401);
        }

        $code = strtoupper(trim($request->input('giftcode', '')));

        if (empty($code)) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Vui lòng nhập mã Giftcode!'
            ]);
        }

        if (!preg_match('/^[A-Z0-9]+$/', $code)) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Mã Giftcode không hợp lệ (chỉ gồm chữ cái và số)!'
            ]);
        }

        $user = Auth::user();
        $ip = $request->ip();

        // Kiểm tra giới hạn nhập theo IP trong ngày (tối đa 20 lần)
        $todayCount = MissionHistory::where('ip_address', $ip)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 30) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Bạn đã đạt giới hạn nhận thưởng trong ngày hôm nay. Vui lòng quay lại vào ngày mai!'
            ]);
        }

        // Tìm mã giftcode
        $giftcode = MissionGiftcode::where('code', $code)->first();

        if (!$giftcode) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Mã Giftcode không tồn tại hoặc đã bị xóa!'
            ]);
        }

        if ($giftcode->status !== 'active' || $giftcode->usages_left < 1) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Mã Giftcode này đã hết lượt sử dụng!'
            ]);
        }

        if ($giftcode->expires_at && $giftcode->expires_at->isPast()) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Mã Giftcode này đã hết hạn sử dụng!'
            ]);
        }

        // Kiểm tra xem tài khoản này đã từng nhận mã này chưa
        $alreadyUsed = MissionHistory::where('user_id', $user->id)
            ->where('code', $code)
            ->exists();

        if ($alreadyUsed) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Bạn đã đổi thưởng mã Giftcode này rồi!'
            ]);
        }

        $rewardAmount = (float)$giftcode->reward_amount;

        try {
            DB::beginTransaction();

            // Trừ số lượt sử dụng
            $giftcode->usages_left -= 1;
            if ($giftcode->usages_left <= 0) {
                $giftcode->status = 'used';
            }
            $giftcode->save();

            // Tăng số lượt hoàn thành của nhiệm vụ
            if ($giftcode->mission_id) {
                Mission::where('id', $giftcode->mission_id)->increment('total_completed');
            }

            // Cộng tiền vào tài khoản người dùng
            $balanceBefore = $user->balance;
            $user->increment('balance', $rewardAmount);
            $balanceAfter = $balanceBefore + $rewardAmount;

            // Ghi nhận biến động số dư trong MoneyTransaction
            MoneyTransaction::create([
                'user_id' => $user->id,
                'type' => 'mission_reward',
                'amount' => $rewardAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => 'Nhận thưởng nhiệm vụ kiếm tiền (Mã: ' . $code . ')',
                'reference_id' => $giftcode->id
            ]);

            // Ghi nhận lịch sử nhiệm vụ
            MissionHistory::create([
                'user_id' => $user->id,
                'mission_id' => $giftcode->mission_id,
                'code' => $code,
                'reward_amount' => $rewardAmount,
                'ip_address' => $ip,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'msg' => 'Đổi thưởng thành công! Bạn nhận được +' . number_format($rewardAmount) . ' VNĐ vào tài khoản.',
                'new_balance' => number_format($balanceAfter) . ' VNĐ',
                'reward' => $rewardAmount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'msg' => 'Có lỗi xảy ra trong quá trình cộng thưởng, vui lòng thử lại!'
            ]);
        }
    }

    /**
     * Lịch sử làm nhiệm vụ
     */
    public function history()
    {
        $user = Auth::user();
        $histories = MissionHistory::with('mission')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('user.mission.history', compact('histories'));
    }
}
