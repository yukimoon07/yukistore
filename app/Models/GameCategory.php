<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'tag_image',
        'description',
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
        'guarantee_3'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function gameGroup()
    {
        return $this->belongsTo(GameGroup::class, 'game_group_id');
    }
}
