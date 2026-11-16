<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionGiftcode extends Model
{
    use HasFactory;

    protected $table = 'mission_giftcodes';

    protected $fillable = [
        'mission_id',
        'code',
        'reward_amount',
        'usages_left',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'reward_amount' => 'float',
        'usages_left' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
