<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'business_unit_id',
        'name',
        'slug',
        'image',
        'gallery',
        'description',
        'price',
        'stock',
        'is_visible',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'gallery'    => 'array',
        'price'      => 'integer',
        'stock'      => 'integer',
        'is_visible' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function logs()
    {
        return $this->hasMany(ProductLog::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)->where('is_active', true);
    }
}
