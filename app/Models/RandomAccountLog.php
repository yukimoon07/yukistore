<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RandomAccountLog extends Model
{
    protected $fillable = [
        'random_account_id',
        'admin_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function randomAccount()
    {
        return $this->belongsTo(RandomCategoryAccount::class, 'random_account_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
