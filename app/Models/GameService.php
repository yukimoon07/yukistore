<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'description',
        'type',
        'active'
    ];

    // Quan hệ với các package
    public function packages()
    {
        return $this->hasMany(ServicePackage::class, 'game_service_id')->orderBy('id', 'asc');
    }

    /**
     * Các gói dịch vụ ĐANG BẬT và có giá hợp lệ (> 0).
     * Dùng cho hiển thị phía khách hàng — gói chưa có giá sẽ không hiển thị.
     */
    public function activePackages()
    {
        return $this->packages()
            ->where('active', true)
            ->where('price', '>', 0);
    }

    /**
     * Giá thấp nhất trong các gói hợp lệ (null nếu chưa có gói nào).
     */
    public function getMinPriceAttribute()
    {
        return $this->activePackages()->min('price');
    }

    /**
     * Dịch vụ có thể đặt được hay không (phải còn ít nhất một gói hợp lệ).
     */
    public function getIsOrderableAttribute(): bool
    {
        return $this->activePackages()->exists();
    }
}