<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    use HasFactory;

    protected $table = 'missions';

    protected $fillable = [
        'title',
        'slug',
        'image',
        'service_type',
        'target_url',
        'api_token',
        'reward_min',
        'reward_max',
        'cooldown_seconds',
        'description',
        'guide_steps',
        'status',
        'total_completed',
    ];

    protected $casts = [
        'reward_min' => 'float',
        'reward_max' => 'float',
        'cooldown_seconds' => 'integer',
        'status' => 'boolean',
        'total_completed' => 'integer',
    ];

    public function giftcodes(): HasMany
    {
        return $this->hasMany(MissionGiftcode::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(MissionHistory::class);
    }
}
