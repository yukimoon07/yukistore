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

class FacebookController extends Controller
{
    public function __construct()
    {
        $clientId = config_get('login_social.facebook.client_id') ?: env('FACEBOOK_CLIENT_ID', '713944317580357');
        $clientSecret = config_get('login_social.facebook.client_secret') ?: env('FACEBOOK_CLIENT_SECRET', '481beed7538a8b7318c45e94401f4e3c');
        $redirect = config_get('login_social.facebook.redirect') ?: (env('FACEBOOK_REDIRECT_URI') ?: url('/auth/facebook/callback'));

        if ($clientId && $clientSecret) {
            config([
                'services.facebook.client_id' => $clientId,
                'services.facebook.client_secret' => $clientSecret,
                'services.facebook.redirect' => $redirect,
            ]);
        }
    }

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToFacebook()
    {
        try {
            if (empty(config('services.facebook.client_id'))) {
                return redirect()->route('login')->with('error', 'Chức năng đăng nhập Facebook đang được cấu hình. Vui lòng đăng nhập bằng tài khoản.');
            }
            return Socialite::driver('facebook')->redirect();
        } catch (\Exception $e) {
            Log::error('Facebook OAuth redirect error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Không thể kết nối đến Facebook. Vui lòng thử lại sau.');
        }
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            // Check if the user already exists in the system
            $existingUser = User::where('email', $facebookUser->email)->first();
            if ($existingUser) {
                // Tài khoản bị khóa → từ chối đăng nhập
                if ($existingUser->banned) {
                    return redirect()->route('login')
                        ->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.');
                }

                // User exists, update facebook_id if not present
                if (empty($existingUser->facebook_id)) {
                    $existingUser->facebook_id = $facebookUser->id;
                    $existingUser->save();
                }

                Auth::login($existingUser);
                return redirect()->intended(route('home'));
            } else {
                // Create a unique username from email
                $baseName = explode('@', $facebookUser->email)[0];
                $username = $baseName;
                $counter = 1;

                while (User::where('username', $username)->exists()) {
                    $username = $baseName . $counter;
                    $counter++;
                }


                // Create a new user
                $newUser = User::create([
                    'username' => $username,
                    'email' => $facebookUser->email,
                    'facebook_id' => $facebookUser->id,
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                    'balance' => 0
                ]);

                Auth::login($newUser);
                return redirect()->intended(route('home'))
                    ->with('success', 'Bạn đã đăng ký tài khoản thành công');
            }

        } catch (Exception $e) {
            Log::error('Facebook login error: ' . $e->getMessage());
            // dd($e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Đăng nhập thất bại. Vui lòng thử lại sau.');
        }
    }
}