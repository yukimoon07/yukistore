<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('user.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Chống spam đăng ký: tối đa 5 tài khoản / phút / IP
        $throttleKey = 'register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => "Bạn đã đăng ký quá nhiều tài khoản. Vui lòng thử lại sau {$seconds} giây.",
            ]);
        }

        $validated = $request->validate([
            // Username: 3-24 ký tự, chữ/số/gạch dưới, không bắt đầu bằng số
            'username' => [
                'required',
                'string',
                'min:3',
                'max:24',
                'regex:/^[a-zA-Z][a-zA-Z0-9_]+$/',
                Rule::unique(User::class),
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'captcha' => [app()->environment('testing') ? 'nullable' : 'required', function ($attribute, $value, $fail) {
                if (!app()->environment('testing') && !\App\Helpers\CaptchaHelper::verify($value, 'register')) {
                    $fail('Mã xác thực (Captcha) không chính xác.');
                }
            }],
        ], [
            'captcha.required'  => 'Vui lòng nhập mã bảo vệ (Captcha).',
            'username.required' => 'Vui lòng nhập tên tài khoản.',
            'username.min'      => 'Tên tài khoản tối thiểu 3 ký tự.',
            'username.max'      => 'Tên tài khoản tối đa 24 ký tự.',
            'username.regex'    => 'Tên tài khoản chỉ gồm chữ, số, gạch dưới và bắt đầu bằng chữ cái.',
            'username.unique'   => 'Tên tài khoản đã tồn tại.',
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không hợp lệ.',
            'email.unique'      => 'Email đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed'=> 'Xác nhận mật khẩu không khớp.',
        ]);

        $referrerId = null;
        if ($request->hasCookie('affiliate_ref')) {
            $refId = (int) $request->cookie('affiliate_ref');
            // Chỉ nhận referrer tồn tại và không tự giới thiệu chính mình
            if ($refId > 0 && User::find($refId)) {
                $referrerId = $refId;
            }
        }

        $user = User::create([
            'username'    => $validated['username'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'ip_address'  => $request->ip(),
            'referrer_id' => $referrerId,
        ]);
        // role mặc định 'member' — không bao giờ nhận từ input (chống tự nâng quyền)

        event(new Registered($user));

        RateLimiter::hit($throttleKey, 60);

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
