<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameAccountLog extends Model
{
    protected $fillable = [
        'game_account_id',
        'admin_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function gameAccount()
    {
        return $this->belongsTo(GameAccount::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
