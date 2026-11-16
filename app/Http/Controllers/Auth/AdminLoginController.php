<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Admin Login — đăng nhập riêng cho quản trị viên.
 *
 * Luồng: POST /admin/login → xác thực username+password (throttle 5 lần/phút)
 * → nếu admin bật 2FA → màn hình nhập OTP (challenges.two_factor)
 * → mã đúng mới đăng nhập thật sự + regenerate session + ghi Activity Log.
 */
class AdminLoginController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor)
    {
    }

    public function show()
    {
        if (Auth::check() && Auth::user()->canAccessAdminPanel()) {
            return redirect()->route('admin.index');
        }

        return view('admin.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Throttle riêng cho admin login: 5 lần/phút theo IP+username
        $throttleKey = 'admin-login:' . strtolower($request->input('username')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            AdminActivityLog::record('login_failed', null, [], ['reason' => 'rate_limited']);

            throw ValidationException::withMessages([
                'username' => "Quá nhiều lần thử. Vui lòng thử lại sau {$seconds} giây.",
            ]);
        }

        $usernameField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $usernameField => $request->username,
            'password' => $request->password,
        ];

        if (! Auth::attempt($credentials, false)) {
            RateLimiter::hit($throttleKey, 60);
            AdminActivityLog::record('login_failed', null, [], ['username' => $request->username]);

            throw ValidationException::withMessages([
                'username' => 'Thông tin đăng nhập không đúng.',
            ]);
        }

        $user = Auth::user();

        // Không phải admin/dev → đăng xuất ngay, thông báo chung chung (không tiết lộ account tồn tại)
        if (! $user->canAccessAdminPanel()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'username' => 'Thông tin đăng nhập không đúng.',
            ]);
        }

        if ($user->banned) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => 'Tài khoản đã bị khóa.',
            ]);
        }

        // Chưa bật 2FA → vào luôn (sẽ được yêu cầu bật trong profile)
        if (! $user->hasTwoFactorEnabled()) {
            $this->finishLogin($request, $user);

            return redirect()->intended(route('admin.index'));
        }

        // Đã bật 2FA → lưu "pending" vào session, chưa đăng nhập chính thức
        RateLimiter::clear($throttleKey);
        $request->session()->put('2fa:user:id', $user->id);
        $request->session()->put('2fa:remember', false);
        Auth::logout();

        return redirect()->route('admin.2fa.challenge');
    }

    /** Màn hình nhập OTP */
    public function challenge()
    {
        if (! session('2fa:user:id')) {
            return redirect()->route('admin.login');
        }

        return view('admin.two-factor-challenge');
    }

    /** Xác minh OTP */
    public function verifyChallenge(Request $request)
    {
        $userId = session('2fa:user:id');
        if (! $userId) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = \App\Models\User::find($userId);
        if (! $user || ! $user->hasTwoFactorEnabled()) {
            $request->session()->forget('2fa:user:id');

            return redirect()->route('admin.login');
        }

        // Throttle OTP: 5 lần/phút
        $throttleKey = 'admin-2fa:' . $user->id . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'code' => "Quá nhiều lần thử. Vui lòng thử lại sau {$seconds} giây.",
            ]);
        }

        $code = trim($request->input('code'));

        $isValid = $this->twoFactor->verify((string) $user->getTwoFactorSecret(), $code);

        // Không phải OTP → thử như recovery code (định dạng XXXXX-XXXXX)
        if (! $isValid && preg_match('/^[A-Z0-9]{5}-[A-Z0-9]{5}$/i', $code)) {
            $isValid = $user->consumeRecoveryCode(strtoupper($code));
        }

        if (! $isValid) {
            RateLimiter::hit($throttleKey, 60);
            AdminActivityLog::record('2fa_failed', $user);

            throw ValidationException::withMessages([
                'code' => 'Mã xác thực không đúng.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->forget('2fa:user:id');

        Auth::login($user, false);
        $this->finishLogin($request, $user);

        return redirect()->intended(route('admin.index'));
    }

    private function finishLogin(Request $request, $user): void
    {
        $request->session()->regenerate();
        AdminActivityLog::record('login', $user);
    }
}
