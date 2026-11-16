<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessUnit extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'image',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Danh mục tài khoản chính thuộc nhóm này */
    public function gameCategories()
    {
        return $this->hasMany(GameCategory::class);
    }

    /** Danh mục tài khoản random thuộc nhóm này */
    public function randomCategories()
    {
        return $this->hasMany(RandomCategory::class);
    }

    /** Dịch vụ cày thuê thuộc nhóm này */
    public function gameServices()
    {
        return $this->hasMany(GameService::class);
    }

    /** Sản phẩm (Tools / Cloud Phone / Other) thuộc nhóm này */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /** Đếm số sản phẩm đang hiển thị trong nhóm (mọi loại) — dùng cho card homepage */
    public function activeItemsCount(): int
    {
        $count = 0;
        $count += $this->gameCategories()->where('active', true)
            ->whereHas('accounts', fn ($q) => $q->where('stock_status', 'available'))->count();
        $count += $this->randomCategories()->where('active', true)
            ->whereHas('accounts', fn ($q) => $q->where('stock_status', 'available'))->count();
        $count += $this->gameServices()->where('active', true)->count();
        $count += $this->products()->where('is_visible', true)->where('is_active', true)->count();

        return $count;
    }
}
