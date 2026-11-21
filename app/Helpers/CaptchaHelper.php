<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class CaptchaHelper
{
    /**
     * Tạo câu hỏi captcha đơn giản lưu vào session
     */
    public static function generate(string $key = 'default'): array
    {
        $a = rand(2, 9);
        $b = rand(1, 9);
        $sum = $a + $b;
        Session::put("captcha_{$key}", $sum);

        return [
            'question' => "{$a} + {$b} = ?",
            'answer' => $sum,
        ];
    }

    /**
     * Kiểm tra câu trả lời captcha
     */
    public static function verify($input, string $key = 'default'): bool
    {
        if (app()->environment('testing')) {
            return true;
        }

        $expected = Session::get("captcha_{$key}");
        if ($expected === null) {
            return false;
        }

        $valid = ((int) $input === (int) $expected);
        if ($valid) {
            Session::forget("captcha_{$key}");
        }

        return $valid;
    }
}
