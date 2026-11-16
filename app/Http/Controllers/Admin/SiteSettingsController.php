<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Cài đặt nâng cao (chuyên sâu): chế độ bảo trì, giới hạn rút tiền,
 * biên lợi nhuận ước tính, bật/tắt popup chào mừng.
 * Toàn bộ giá trị lưu ở bảng configs qua config_set().
 */
class SiteSettingsController extends Controller
{
    public function index()
    {
        $title = 'Cài đặt nâng cao';

        $configs = [
            'site_maintenance'        => (bool) config_get('site_maintenance', false),
            'site_maintenance_message' => config_get('site_maintenance_message', 'Website đang bảo trì, vui lòng quay lại sau ít phút.'),
            'welcome_modal'           => (bool) config_get('welcome_modal', false),
            'min_withdraw_money'      => (int) config_get('min_withdraw_money', 100000),
            'max_withdraw_money'      => (int) config_get('max_withdraw_money', 10000000),
            'estimated_profit_margin' => (float) config_get('estimated_profit_margin', 0.4),
        ];

        return view('admin.settings.advanced', compact('title', 'configs'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_maintenance'         => 'nullable',
            'site_maintenance_message' => 'nullable|string|max:500',
            'welcome_modal'            => 'nullable',
            'min_withdraw_money'       => 'required|integer|min:0',
            'max_withdraw_money'       => 'required|integer|gte:min_withdraw_money',
            'estimated_profit_margin'  => 'required|numeric|between:0,1',
        ], [
            'max_withdraw_money.gte' => 'Mức rút tối đa phải lớn hơn hoặc bằng mức rút tối thiểu.',
            'estimated_profit_margin.between' => 'Biên lợi nhuận phải nằm trong khoảng 0 - 1 (vd: 0.4 = 40%).',
        ]);

        $old = [
            'site_maintenance'        => config_get('site_maintenance', false),
            'min_withdraw_money'      => config_get('min_withdraw_money', 100000),
            'max_withdraw_money'      => config_get('max_withdraw_money', 10000000),
            'estimated_profit_margin' => config_get('estimated_profit_margin', 0.4),
        ];

        $maintenanceOn = $request->has('site_maintenance');

        try {
            config_set('site_maintenance', $maintenanceOn ? '1' : '0');
            config_set('site_maintenance_message', $validated['site_maintenance_message'] ?? 'Website đang bảo trì, vui lòng quay lại sau ít phút.');
            config_set('welcome_modal', $request->has('welcome_modal') ? '1' : '0');
            config_set('min_withdraw_money', (int) $validated['min_withdraw_money']);
            config_set('max_withdraw_money', (int) $validated['max_withdraw_money']);
            config_set('estimated_profit_margin', (float) $validated['estimated_profit_margin']);

            // Đồng bộ với cơ chế maintenance mode của Laravel (artisan down/up)
            // để toàn bộ route bị chặn bằng trang 503 (trừ admin).
            try {
                if ($maintenanceOn) {
                    Artisan::call('down', [
                        '--secret' => $this->maintenanceSecret(),
                        '--render' => 'errors.503',
                    ]);
                } else {
                    Artisan::call('up');
                }
            } catch (\Throwable $e) {
                Log::warning('Không thể đồng bộ maintenance mode: ' . $e->getMessage());
            }

            AdminActivityLog::record('settings.advanced.update', null, $old, [
                'site_maintenance'        => $maintenanceOn,
                'min_withdraw_money'      => (int) $validated['min_withdraw_money'],
                'max_withdraw_money'      => (int) $validated['max_withdraw_money'],
                'estimated_profit_margin' => (float) $validated['estimated_profit_margin'],
            ]);

            return redirect()->route('admin.settings.advanced')
                ->with('success', 'Cài đặt nâng cao đã được cập nhật thành công.');
        } catch (\Throwable $e) {
            Log::error('Lỗi cập nhật cài đặt nâng cao: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật cài đặt.')->withInput();
        }
    }

    /**
     * Secret để admin (đã đăng nhập) vẫn xem được site khi bật bảo trì.
     */
    private function maintenanceSecret(): string
    {
        $secret = config_get('maintenance_secret');
        if (empty($secret)) {
            $secret = bin2hex(random_bytes(8));
            config_set('maintenance_secret', $secret);
        }
        return $secret;
    }
}
