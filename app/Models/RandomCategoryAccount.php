<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class RandomCategoryAccount extends Model
{
    protected $table = 'random_category_accounts';

    protected $fillable = [
        'random_category_id',
        'account_name',
        'password',
        'price',
        'status',
        'server',
        'buyer_id',
        'note',
        'note_buyer',
        'thumbnail',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Mật khẩu tài khoản game là dữ liệu nhạy cảm — luôn mã hóa trước khi lưu.
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
     * Giải mã khi đọc. Bản ghi cũ chưa mã hóa trả nguyên giá trị.
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
                report($e);
                return null;
            }
        }

        return $value;
    }

    public function category()
    {
        return $this->belongsTo(RandomCategory::class, 'random_category_id');
    }

    public function randomCategory()
    {
        return $this->belongsTo(RandomCategory::class, 'random_category_id');
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
