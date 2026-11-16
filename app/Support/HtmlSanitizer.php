<?php

namespace App\Support;

/**
 * Làm sạch HTML theo allowlist trước khi render bằng {!! !!}.
 *
 * Mục tiêu: chặn XSS trong nội dung do Admin nhập (ghi chú, thông báo, mô tả, tin tức)
 * mà vẫn cho phép các thẻ định dạng cơ bản.
 *
 * Lưu ý: đây là lớp phòng thủ CHO NỘI DUNG TIN CẬY MỨC TRUNG BÌNH (Admin).
 * Nội dung do khách hàng nhập KHÔNG được render dạng HTML — luôn dùng {{ }}.
 */
class HtmlSanitizer
{
    /** Thẻ được phép giữ lại */
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'small', 'sub', 'sup',
        'ul', 'ol', 'li', 'blockquote', 'hr',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'a', 'span', 'div', 'code', 'pre',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'img',
    ];

    /** Thuộc tính được phép theo từng thẻ */
    private const ALLOWED_ATTRS = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'span' => ['class'],
        'div' => ['class'],
        'p' => ['class'],
        'table' => ['class'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan'],
    ];

    /**
     * Làm sạch một chuỗi HTML.
     */
    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        // 1. Bỏ hoàn toàn các khối nguy hiểm kèm nội dung bên trong
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|select|textarea|meta|link|base|svg|math)\b[^>]*>.*?</\1>#is', '', $html) ?? '';
        // 2. Bỏ thẻ đơn của các loại trên (không có nội dung)
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|select|textarea|meta|link|base)\b[^>]*/?>#is', '', $html) ?? '';

        // 3. Bỏ comment HTML (có thể chứa conditional comment IE)
        $html = preg_replace('#<!--.*?-->#s', '', $html) ?? '';

        // 4. Xử lý từng thẻ: giữ thẻ trong allowlist với thuộc tính allowlist, bỏ phần còn lại
        $html = preg_replace_callback(
            '#<\s*(/?)\s*([a-zA-Z0-9]+)((?:\s+[^>]*)?)\s*(/?)\s*>#',
            function (array $m): string {
                $closing = $m[1] === '/';
                $tag = strtolower($m[2]);
                $attrString = $m[3] ?? '';
                $selfClose = $m[4] === '/';

                if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                    return ''; // Loại bỏ thẻ không được phép
                }

                if ($closing) {
                    return "</{$tag}>";
                }

                $safeAttrs = self::filterAttributes($tag, $attrString);
                $void = in_array($tag, ['br', 'hr', 'img'], true);

                return '<' . $tag . $safeAttrs . ($void ? ' />' : '>');
            },
            $html
        ) ?? '';

        // 5. Chặn javascript: và data: nguy hiểm còn sót trong thuộc tính href/src
        $html = preg_replace('#\s(href|src)\s*=\s*(["\']?)\s*(javascript|vbscript|data)\s*:#i', ' $1=$2#', $html) ?? '';

        return $html;
    }

    /**
     * Nội dung được render trong <pre> cho JSON-LD hoặc mô tả kỹ thuật.
     */
    public static function cleanStrict(?string $html): string
    {
        return strip_tags(self::clean($html), '<p><br><strong><b><em><i><u><ul><ol><li><a>');
    }

    /**
     * Lọc thuộc tính của một thẻ theo allowlist.
     */
    private static function filterAttributes(string $tag, string $attrString): string
    {
        if (trim($attrString) === '') {
            return '';
        }

        $allowed = self::ALLOWED_ATTRS[$tag] ?? [];
        if (empty($allowed)) {
            return '';
        }

        $attributes = [];
        preg_match_all(
            '#([a-zA-Z_:][-a-zA-Z0-9_:.]*)\s*(?:=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>]+)))?#',
            $attrString,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $attr) {
            $name = strtolower($attr[1]);
            $value = $attr[2] !== '' ? $attr[2] : ($attr[3] !== '' ? $attr[3] : ($attr[4] ?? ''));

            if (!in_array($name, $allowed, true)) {
                continue;
            }

            // Bỏ mọi thuộc tính sự kiện (onclick, onerror...) — đã bị loại bởi allowlist,
            // nhưng kiểm tra thêm để chắc chắn.
            if (str_starts_with($name, 'on')) {
                continue;
            }

            // Chuẩn hóa URL
            if (in_array($name, ['href', 'src'], true)) {
                $value = trim($value);
                // Chỉ cho phép http(s), mailto, tel và đường dẫn tương đối
                if ($value !== '' && !preg_match('#^(https?://|mailto:|tel:|/|\#)#i', $value)) {
                    continue;
                }
                if (preg_match('#^\s*(javascript|vbscript|data)\s*:#i', $value)) {
                    continue;
                }
            }

            if (in_array($name, ['width', 'height', 'colspan', 'rowspan'], true) && !preg_match('#^\d{1,4}$#', $value)) {
                continue;
            }

            if ($name === 'class' && !preg_match('#^[a-zA-Z0-9_\-\s]+$#', $value)) {
                continue;
            }

            $safeValue = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $attributes[] = $name . '="' . $safeValue . '"';
        }

        return empty($attributes) ? '' : ' ' . implode(' ', $attributes);
    }
}