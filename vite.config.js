import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

/**
 * Vite là pipeline duy nhất được giữ lại (Laravel Mix đã bị loại bỏ).
 *
 * Blade hiện tại KHÔNG dùng @vite() — CSS/JS được phục vụ tĩnh từ
 * public/css và public/js. Entry dưới đây tồn tại để `npm run build`
 * thành công và để có chỗ mở rộng.
 */
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
