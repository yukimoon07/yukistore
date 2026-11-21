<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RandomCategory extends Model
{
    protected $table = 'random_categories';

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'tag_image',
        'description',
        'price',
        'active',
        'platform',
        'game_group_id',
        'is_flash_sale',
        'flash_sale_old_price',
        'flash_sale_new_price',
        'flash_sale_end_time',
        'custom_lead',
        'custom_badge',
        'guarantee_1',
        'guarantee_2',
        'guarantee_3',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function accounts()
    {
        return $this->hasMany(RandomCategoryAccount::class, 'random_category_id');
    }

    public function gameGroup()
    {
        return $this->belongsTo(GameGroup::class, 'game_group_id');
    }
}
