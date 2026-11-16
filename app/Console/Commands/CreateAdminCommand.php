<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Tạo / cập nhật tài khoản quản trị một cách an toàn.
 *
 * Nguyên tắc bảo mật:
 *  - KHÔNG có mật khẩu mặc định hardcode trong mã nguồn.
 *  - Khi tạo mới, nếu người dùng không nhập mật khẩu thì hệ thống tự sinh
 *    một mật khẩu mạnh 16 ký tự và chỉ hiển thị ĐÚNG MỘT LẦN trên màn hình.
 *    Mật khẩu này không được ghi vào log và không được lưu dạng rõ ở bất kỳ đâu.
 *  - Mật khẩu nhập tay được nhập ở chế độ ẩn và phải xác nhận hai lần,
 *    tránh lưu vào history của shell.
 *
 * Cách dùng:
 *   php artisan admin:create-admin
 *   php artisan admin:create-admin --username=nvadmin --email=admin@shop.local
 *   php artisan admin:create-admin --reset-password
 */
class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create-admin
        {--username= : Tên đăng nhập quản trị}
        {--email= : Email quản trị}
        {--password= : Mật khẩu (để trống nếu muốn tự sinh ngẫu nhiên; không khuyến nghị)}
        {--reset-password : Đặt lại mật khẩu cho tài khoản đã tồn tại}
        {--force : Nâng tài khoản dev/member hiện có lên admin}
        {--no-confirm : Không hỏi xác nhận (dùng trong script tự động)}';

    protected $description = 'Tạo hoặc cập nhật tài khoản quản trị (không dùng mật khẩu mặc định công khai)';

    public function handle(): int
    {
        $username = $this->option('username') ?: $this->ask('Tên đăng nhập quản trị');
        $email = $this->option('email') ?: $this->ask(
            'Email quản trị',
            'admin@' . (parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost')
        );

        if (!$username || !$email) {
            $this->error('Thiếu tên đăng nhập hoặc email.');
            return self::INVALID;
        }

        $validator = Validator::make(compact('username', 'email'), [
            'username' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-zA-Z0-9_.-]+$/'],
            'email' => ['required', 'email', 'max:190'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::INVALID;
        }

        $existing = User::where('username', $username)->orWhere('email', $email)->first();

        if ($existing && $existing->role !== 'admin' && !$this->option('force')) {
            $this->warn("Tài khoản '$username' đang có vai trò '{$existing->role}'.");
            if (!$this->confirm('Nâng tài khoản này lên admin?', false)) {
                $this->info('Đã hủy. Dùng --force để bỏ qua câu hỏi này.');
                return self::SUCCESS;
            }
        }

        // Chỉ hỏi mật khẩu khi: tài khoản mới, hoặc đang reset, hoặc có truyền --password.
        $password = $this->option('password');
        $shouldSetPassword = !$existing || $this->option('reset-password') || (bool) $password;
        $generatedPassword = null;

        if ($shouldSetPassword) {
            if (!$password && !$this->option('no-interaction')) {
                $password = $this->secret('Mật khẩu (để trống = tự sinh ngẫu nhiên)');
                if ($password) {
                    $confirm = $this->secret('Xác nhận mật khẩu');
                    if ($password !== $confirm) {
                        $this->error('Mật khẩu xác nhận không khớp.');
                        return self::INVALID;
                    }
                }
            }

            if (!$password) {
                $generatedPassword = $this->generateStrongPassword();
                $password = $generatedPassword;
            }

            if (strlen($password) < 8) {
                $this->error('Mật khẩu phải có ít nhất 8 ký tự.');
                return self::INVALID;
            }
        }

        if ($existing) {
            $existing->fill([
                'email' => $email,
                'role' => 'admin',
                'banned' => false,
                'email_verified_at' => $existing->email_verified_at ?? now(),
            ]);

            if ($shouldSetPassword) {
                $existing->password = Hash::make($password);
            }

            $existing->save();
            $this->info("Đã cập nhật tài khoản quản trị: {$existing->username} ({$existing->email})");
        } else {
            $user = User::create([
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'balance' => 0,
                'total_deposited' => 0,
                'banned' => false,
                'ip_address' => request()->ip(),
                'email_verified_at' => now(),
            ]);

            $this->info("Đã tạo tài khoản quản trị: {$user->username} ({$user->email})");
        }

        if ($generatedPassword) {
            $this->newLine();
            $this->warn('=== MẬT KHẨU TỰ SINH — CHỈ HIỂN THỊ MỘT LẦN ===');
            $this->line($generatedPassword);
            $this->warn('Sao chép ngay bây giờ. Mật khẩu không được lưu dạng rõ ở bất kỳ đâu.');
            $this->warn('Khuyến nghị: đổi mật khẩu và bật 2FA ngay sau lần đăng nhập đầu.');
            $this->newLine();
        }

        $this->line('Mẹo: bật xác thực hai lớp (2FA) sau khi đăng nhập tại /admin/security/two-factor.');
        return self::SUCCESS;
    }

    /**
     * Sinh mật khẩu mạnh 16 ký tự (bao gồm chữ hoa, chữ thường, số, ký tự đặc biệt).
     */
    private function generateStrongPassword(): string
    {
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $digits = '23456789';
        $symbols = '!@#$%^&*()-_=+';
        $all = $upper . $lower . $digits . $symbols;

        $password = '';
        $password .= $upper[random_int(0, strlen($upper) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $digits[random_int(0, strlen($digits) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        for ($i = 4; $i < 16; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        // Fisher-Yates dùng random_int (không đoán được)
        $chars = str_split($password);
        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return implode('', $chars);
    }
}