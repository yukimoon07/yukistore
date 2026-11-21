<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\TwoFactorAuthenticatable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'phone',
        'password',
        'email',
        'google_id',
        'facebook_id',
        'role',
        'balance',
        'total_deposited',
        'banned',
        'ip_address',
        'email_verified_at',
        'avatar',
        'referrer_id',
        'total_commission'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Kiểm tra có phải tài khoản admin (quản trị viên cấp cao) hay không.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Kiểm tra có phải tài khoản dev (viên chức điều hành, yếu hơn admin) hay không.
     *
     * @return bool
     */
    public function isDev(): bool
    {
        return $this->role === 'dev';
    }

    /**
     * Kiểm tra có quyền vào admin panel hay không (admin hoặc dev đều được).
     *
     * @return bool
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->role === 'admin' || $this->role === 'dev';
    }

    /**
     * Alias của isAdmin(), dùng ở những nơi muốn diễn đạt rõ nghĩa là "quản trị cấp cao".
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Kiểm tra có quyền quản lý cài đặt hệ thống hay không (chỉ admin).
     *
     * @return bool
     */
    public function canManageSettings(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Kiểm tra có quyền cấp/thu quyền (đổi role) cho người dùng hay không (chỉ admin).
     *
     * @return bool
     */
    public function canAssignRoles(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Kiểm tra tài khoản có thuộc một trong các vai trò được liệt kê hay không.
     *
     * @param array<string> $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Gửi thông báo đặt lại mật khẩu với token
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        try {
            $this->notify(new ResetPasswordNotification($token));
            Log::info('Đã gửi email đặt lại mật khẩu thành công', ['user_id' => $this->id, 'email' => $this->email]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi email đặt lại mật khẩu', [
                'user_id' => $this->id,
                'email' => $this->email,
                'error' => $e->getMessage()
            ]);

            // Không ném ngoại lệ để không làm gián đoạn luồng người dùng
            // Người dùng vẫn có thể nhận được token thông qua URL trong trường hợp email không gửi được
        }
    }
}
