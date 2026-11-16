<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'logo',
        'instructions',
        'config',
        'sort_order',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /** Các trường trong config KHÔNG ĐƯỢC trả ra frontend */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true)->orderBy('sort_order')->orderBy('id');
    }

    /** Dữ liệu an toàn cho frontend */
    public function toPublicArray(): array
    {
        return [
            'id'           => $this->id,
            'code'         => $this->code,
            'name'         => $this->name,
            'logo'         => $this->logo,
            'instructions' => $this->instructions,
            'sort_order'   => $this->sort_order,
        ];
    }

    /** Giải mã config (server-only) */
    public function configData(): array
    {
        if (empty($this->config)) {
            return [];
        }

        try {
            return json_decode(Crypt::decryptString($this->config), true) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Mã hóa + lưu config từ mảng plain */
    public function setConfigData(array $data): void
    {
        $this->config = $data ? Crypt::encryptString(json_encode($data)) : null;
    }
}
