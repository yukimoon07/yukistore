<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'target_type',
        'target_id',
        'old_values',
        'new_values',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Ghi log hành động quản trị (mask dữ liệu nhạy cảm tự động).
     */
    public static function record(
        string $action,
        $target = null,
        array $old = [],
        array $new = []
    ): void {
        $user = auth()->user();

        self::create([
            'admin_id'    => $user?->id,
            'action'      => $action,
            'target_type' => $target ? ($target::class) : null,
            'target_id'   => $target?->id,
            'old_values'  => SecuritySanitizer::mask($old) ?: null,
            'new_values'  => SecuritySanitizer::mask($new) ?: null,
            'ip'          => request()?->ip(),
            'user_agent'  => substr((string) request()?->userAgent(), 0, 500),
        ]);
    }
}
