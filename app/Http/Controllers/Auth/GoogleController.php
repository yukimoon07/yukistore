<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    public function __construct()
    {
        $clientId = config_get('login_social.google.client_id') ?: env('GOOGLE_CLIENT_ID');
        $clientSecret = config_get('login_social.google.client_secret') ?: env('GOOGLE_CLIENT_SECRET');
        $redirect = config_get('login_social.google.redirect') ?: (env('GOOGLE_REDIRECT_URI') ?: url('/auth/google/callback'));

        if ($clientId && $clientSecret) {
            config([
                'services.google.client_id' => $clientId,
                'services.google.client_secret' => $clientSecret,
                'services.google.redirect' => $redirect,
            ]);
        }
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        try {
            if (empty(config('services.google.client_id'))) {
                return redirect()->route('login')->with('error', 'Chức năng đăng nhập Google đang được cấu hình. Vui lòng đăng nhập bằng tài khoản.');
            }
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Google OAuth redirect error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Không thể kết nối đến Google. Vui lòng thử lại sau.');
        }
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if the user already exists in the system
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                // Tài khoản bị khóa → từ chối đăng nhập
                if ($existingUser->banned) {
                    return redirect()->route('login')
                        ->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.');
                }

                // User exists, update google_id if not present
                if (empty($existingUser->google_id)) {
                    $existingUser->google_id = $googleUser->id;
                    $existingUser->save();
                }

                Auth::login($existingUser);
                return redirect()->intended(route('home'));
            } else {
                // Create a unique username from email
                $baseName = explode('@', $googleUser->email)[0];
                $username = $baseName;
                $counter = 1;

                while (User::where('username', $username)->exists()) {
                    $username = $baseName . $counter;
                    $counter++;
                }

                // Create a new user
                $newUser = User::create([
                    'username' => $username,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                    'avatar' => $googleUser->avatar,
                    'balance' => 0
                ]);

                Auth::login($newUser);
                return redirect()->intended(route('home'))
                    ->with('success', 'Bạn đã đăng ký tài khoản thành công');
            }
        } catch (Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Đăng nhập  thất bại. Vui lòng thử lại sau.');
        }
    }
}
