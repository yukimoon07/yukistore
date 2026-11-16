<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionHistory extends Model
{
    use HasFactory;

    protected $table = 'mission_histories';

    protected $fillable = [
        'user_id',
        'mission_id',
        'code',
        'reward_amount',
        'ip_address',
    ];

    protected $casts = [
        'reward_amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
