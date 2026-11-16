<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;

/**
 * Quản lý 2FA của tài khoản admin (trong Profile admin).
 * Luồng: GET /admin/security/2fa → QR + secret → POST confirm (nhập OTP)
 * → hiển thị 8 recovery code 1 lần duy nhất.
 */
class TwoFactorController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor)
    {
    }

    /** Trang thiết lập */
    public function index(Request $request)
    {
        $user = $request->user();

        $secret = session('2fa:setup:secret');
        if (! $secret && ! $user->hasTwoFactorEnabled()) {
            $secret = $this->twoFactor->generateSecret();
            session(['2fa:setup:secret' => $secret]);
        }

        $otpauthUri = $secret
            ? $this->twoFactor->otpauthUri($secret, $user->email ?: $user->username, config('app.name', 'ShopGame'))
            : null;

        return view('admin.two-factor', [
            'title'      => 'Bảo mật hai lớp (2FA)',
            'enabled'    => $user->hasTwoFactorEnabled(),
            'secret'     => $secret ? $this->twoFactor->formatSecretForDisplay($secret) : null,
            'secretRaw'  => $secret,
            'otpauthUri' => $secret
                ? $this->twoFactor->otpauthUri($secret, $user->email ?: $user->username, config('app.name', 'ShopGame'))
                : null,
            'recoveryCodes' => session('2fa:recovery-codes'), // chỉ hiện 1 lần ngay sau khi bật
        ]);
    }

    /** Bật 2FA — xác nhận OTP đầu tiên */
    public function confirm(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        $user = $request->user();
        $secret = session('2fa:setup:secret');

        if (! $secret) {
            return redirect()->route('admin.2fa.index')
                ->with('error', 'Phiên thiết lập đã hết hạn. Vui lòng thử lại.');
        }

        if (! $this->twoFactor->verify($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Mã xác thực không đúng. Vui lòng thử lại.']);
        }

        $user->setTwoFactorSecret($secret);
        $user->two_factor_confirmed_at = now();
        $user->save();

        $recoveryCodes = $user->generateRecoveryCodes();

        session()->forget('2fa:setup:secret');
        // Flash: chỉ tồn tại trong request kế tiếp (1 lần), không lưu lâu trong session
        session()->flash('2fa:recovery-codes', $recoveryCodes);

        AdminActivityLog::record('2fa_enabled', $user);

        return redirect()->route('admin.2fa.index')
            ->with('success', 'Đã bật bảo mật hai lớp. Hãy lưu các mã khôi phục bên dưới — chúng chỉ hiển thị một lần.');
    }

    /** Tạo lại recovery codes */
    public function regenerateRecovery(Request $request)
    {
        $user = $request->user();

        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.2fa.index')->with('error', '2FA chưa được bật.');
        }

        $recoveryCodes = $user->generateRecoveryCodes();
        session()->flash('2fa:recovery-codes', $recoveryCodes);

        AdminActivityLog::record('2fa_recovery_regenerated', $user);

        return redirect()->route('admin.2fa.index')
            ->with('success', 'Đã tạo lại mã khôi phục.');
    }

    /** Tắt 2FA — yêu cầu mật khẩu + OTP hiện tại */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password:web'],
            'code'     => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! $this->twoFactor->verify((string) $user->getTwoFactorSecret(), $request->input('code'))) {
            return back()->withErrors(['code' => 'Mã xác thực không đúng.']);
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        AdminActivityLog::record('2fa_disabled', $user);

        return redirect()->route('admin.2fa.index')->with('success', 'Đã tắt bảo mật hai lớp.');
    }
}
