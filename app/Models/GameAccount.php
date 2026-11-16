<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class GameAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_category_id',
        'account_name',
        'password',
        'price',
        'status',
        'note',
        'thumb',
        'images',
        'details'
    ];

    protected $casts = [
        'details' => 'array',
        'images' => 'array',
    ];

    /**
     * Mật khẩu tài khoản game là dữ liệu nhạy cảm — luôn mã hóa trước khi lưu.
     * Không mã hóa lại nếu giá trị đã là payload Crypt (tránh double-encrypt).
     */
    public function setPasswordAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['password'] = $value;
            return;
        }

        if (is_string($value) && str_starts_with($value, 'eyJ') && substr_count($value, '.') === 2) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = Crypt::encryptString((string) $value);
    }

    /**
     * Giải mã khi đọc. Bản ghi cũ chưa mã hóa trả nguyên giá trị để không làm hỏng luồng hiển thị;
     * sau đó chạy `php artisan accounts:encrypt-passwords` để chuyển đổi.
     */
    public function getPasswordAttribute($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        if (str_starts_with($value, 'eyJ') && substr_count($value, '.') === 2) {
            try {
                return Crypt::decryptString($value);
            } catch (\Throwable $e) {
                // Không giải mã được (APP_KEY đổi / dữ liệu hỏng) — không trả payload ra ngoài.
                report($e);
                return null;
            }
        }

        // Bản ghi cũ dạng plain — chưa chuyển đổi.
        return $value;
    }

    public function category()
    {
        return $this->belongsTo(GameCategory::class, 'game_category_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function getOrderCodeAttribute()
    {
        $timestampHex = dechex($this->created_at ? $this->created_at->timestamp : time());
        $hashHex = substr(md5('order_' . $this->id), 0, 13 - strlen($timestampHex));
        return $timestampHex . $hashHex;
    }
}
