<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\GameAccount;
use App\Models\GameService;
use App\Models\LuckyWheel;
use App\Models\ServiceHistory;
use App\Models\RandomCategory;
use App\Models\RandomCategoryAccount;
use App\Models\MoneyTransaction;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    //
    public function index()
    {
        $gameGroups = \App\Models\GameGroup::where('active', true)->orderBy('order', 'asc')->orderBy('id', 'asc')->get();

        // Danh mục bán acc game — aggregate 1 lần (tránh N+1 trong vòng lặp).
        $categoryStats = GameAccount::select('game_category_id')
            ->selectRaw("SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) AS sold_count")
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) AS available_count")
            ->selectRaw("MIN(CASE WHEN status = 'available' THEN price END) AS min_price")
            ->groupBy('game_category_id')
            ->get()
            ->keyBy('game_category_id');

        $categories = Category::with('gameGroup')->where('active', true)->orderBy('updated_at', 'desc')->get();
        foreach ($categories as $category) {
            $stat = $categoryStats->get($category->id);
            $category->soldCount = (int) ($stat->sold_count ?? 0);
            $category->allAccount = (int) ($stat->available_count ?? 0);
            $category->price = (int) ($stat->min_price ?? 0);
            $category->url = route('category.index', ['slug' => $category->slug]);
        }

        // Random categories — aggregate 1 lần.
        $randomStats = RandomCategoryAccount::select('random_category_id')
            ->selectRaw("SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) AS sold_count")
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) AS available_count")
            ->selectRaw("MIN(CASE WHEN status = 'available' THEN price END) AS min_price")
            ->groupBy('random_category_id')
            ->get()
            ->keyBy('random_category_id');

        // Dịch vụ — kèm số lượt thuê (aggregate 1 lần) và gói hợp lệ cho spotlight.
        $serviceOrderCounts = ServiceHistory::select('game_service_id', DB::raw('COUNT(*) AS order_count'))
            ->groupBy('game_service_id')
            ->pluck('order_count', 'game_service_id');

        $services = GameService::with('activePackages')->where('active', true)->orderBy('updated_at', 'desc')->get();
        foreach ($services as $service) {
            $service->orderCount = (int) ($serviceOrderCounts[$service->id] ?? 0);
        }

        $randomCategories = RandomCategory::with('gameGroup')->where('active', true)->orderBy('updated_at', 'desc')->get();
        foreach ($randomCategories as $category) {
            $stat = $randomStats->get($category->id);
            $category->soldCount = (int) ($stat->sold_count ?? 0);
            $category->allAccount = (int) ($stat->available_count ?? 0);
            $category->price = (int) ($stat->min_price ?? 0);
            $category->url = route('random.index', ['slug' => $category->slug]);
        }

        $categories = $categories->concat($randomCategories);

        // Vòng quay may mắn — đếm lịch sử bằng withCount (tránh lazy-load từng wheel).
        $LuckWheel = LuckyWheel::withCount('histories')->where('active', true)->orderBy('updated_at', 'desc')->get();
        foreach ($LuckWheel as $wheel) {
            $wheel->soldCount = $wheel->histories_count;
        }

        // Lấy 20 giao dịch gần đây
        $recentTransactions = MoneyTransaction::with('user')
            ->whereHas('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Lấy top người nạp tiền nhiều nhất trong tháng hiện tại
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $topDepositors = MoneyTransaction::select('user_id', DB::raw('SUM(amount) as total_amount'))
            ->where('type', 'deposit')
            ->whereHas('user')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('user_id')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();

        // Lấy thông tin người dùng cho top depositors và lọc bỏ tài khoản null
        $topDepositors = $topDepositors->filter(function ($depositor) {
            $depositor->user = \App\Models\User::find($depositor->user_id);
            return $depositor->user !== null;
        });

        $notifications = Notification::orderBy('created_at', 'desc')->get();

        // Đánh giá khách hàng: CHỈ dùng dữ liệu thật từ lịch sử mua hàng.
        // Không có cột nội dung nhận xét trong database, nên chỉ hiển thị
        // thông tin xác thực "đã mua" (không bịa nội dung đánh giá).
        $recentPurchases = $this->getRealReviewEntries(8);

        $activeFlashSale = \App\Models\FlashSale::with(['items.category'])
            ->where('status', true)
            ->where('end_time', '>', now())
            ->where('start_time', '<=', now())
            ->orderBy('end_time', 'asc')
            ->first();

        // Lấy danh sách các chiến dịch trong ngày hoặc sắp tới để làm Timeline
        $timelineCampaigns = \App\Models\FlashSale::where('status', true)
            ->where('end_time', '>', now()->subHours(24)) // Lấy cả những cái vừa diễn ra gần đây
            ->orderBy('start_time', 'asc')
            ->limit(6)
            ->get();

        $flashSales = collect();
        if ($activeFlashSale) {
            foreach ($activeFlashSale->items as $item) {
                if ($item->category && $item->category->active == 1) {
                    $cat = $item->category;
                    $cat->is_random = ($item->item_type == 'random');
                    $cat->flash_sale_old_price = $item->old_price;
                    $cat->flash_sale_new_price = $item->new_price;
                    $cat->flash_sale_end_time = $activeFlashSale->end_time;
                    $flashSales->push($cat);
                }
            }
        }

        $featuredServices = $services->filter(function ($s) {
            return (bool) ($s->is_featured ?? false);
        });
        if ($featuredServices->isEmpty()) {
            $featuredServices = $services->take(4);
        }

        $latestReviews = \App\Models\OrderReview::with(['user', 'serviceOrder.gameService'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        return view('user.home', compact(
            'gameGroups',
            'categories',
            'services',
            'featuredServices',
            'latestReviews',
            'LuckWheel',
            'recentTransactions',
            'topDepositors',
            'notifications',
            'recentPurchases',
            'flashSales',
            'activeFlashSale',
            'timelineCampaigns'
        ));
    }

    public function reviews()
    {
        $purchases = $this->getRealReviewEntries(24, true);

        return view('user.reviews', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * Lấy các lượt mua THẬT gần đây để hiển thị dưới dạng xác thực mua hàng.
     * Tên người mua được che một phần (vd: "abc****ef").
     *
     * @param  int  $limit
     * @param  bool  $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    private function getRealReviewEntries(int $limit, bool $paginate = false)
    {
        $query = \App\Models\PurchaseHistory::with('user')
            ->whereHas('user')
            ->orderBy('created_at', 'desc');

        if ($paginate) {
            $entries = $query->paginate($limit);
        } else {
            $entries = $query->limit($limit)->get();
        }

        $mapEntry = function ($purchase) {
            $username = $purchase->user?->username ?? 'KhachHang';

            return [
                'name' => $this->maskName($username),
                'id' => $purchase->game_account_id,
                'amount' => $purchase->amount,
                'date' => optional($purchase->created_at)->format('d/m/Y'),
            ];
        };

        return $paginate ? $entries->through($mapEntry) : collect($entries)->map($mapEntry);
    }

    /**
     * Che tên người dùng: giữ 3 ký tự đầu, 2 ký tự cuối.
     */
    private function maskName(string $username): string
    {
        $len = mb_strlen($username);

        if ($len <= 4) {
            return mb_substr($username, 0, 1) . '****';
        }

        return mb_substr($username, 0, 3) . '****' . mb_substr($username, -2);
    }

    public function faq()
    {
        return view('user.faq');
    }

    public function terms()
    {
        $title = "Điều Khoản Sử Dụng - Yukistore";
        $raw = config_get('terms_of_use');
        $content = (!empty($raw) && strlen(trim(strip_tags($raw))) > 30) 
            ? \App\Support\HtmlSanitizer::clean($raw) 
            : $this->getDefaultTermsOfUse();
        return view('user.page', compact('title', 'content'));
    }

    public function privacy()
    {
        $title = "Chính Sách Bảo Mật - Yukistore";
        $raw = config_get('privacy_policy');
        $content = (!empty($raw) && strlen(trim(strip_tags($raw))) > 30) 
            ? \App\Support\HtmlSanitizer::clean($raw) 
            : $this->getDefaultPrivacyPolicy();
        return view('user.page', compact('title', 'content'));
    }

    protected function getDefaultTermsOfUse(): string
    {
        return '
        <div class="legal-doc">
            <h2>1. Giới Thiệu Chung</h2>
            <p>Chào mừng quý khách đến với <strong>Yukistore</strong> (yukistore.zcloudviet.xyz). Khi truy cập, đăng ký tài khoản và sử dụng bất kỳ dịch vụ nào trên hệ thống của chúng tôi (bao gồm mua tài khoản game, dịch vụ cày thuê, thử vận may), quý khách đồng ý tuân thủ toàn bộ các điều khoản và quy định được nêu rõ dưới đây.</p>

            <h2>2. Điều Kiện Tài Khoản Người Dùng</h2>
            <ul>
                <li>Khách hàng có trách nhiệm bảo mật thông tin đăng nhập, mật khẩu và mã bảo vệ tài khoản cá nhân.</li>
                <li>Yukistore không chịu trách nhiệm đối với các tổn thất phát sinh do khách hàng tự ý chia sẻ thông tin tài khoản cho bên thứ ba.</li>
                <li>Nghiêm cấm các hành vi gian lận, cố tình khai thác lỗi hệ thống, spam đơn hàng hoặc rửa tiền qua các cổng thanh toán.</li>
            </ul>

            <h2>3. Quy Định Giao Dịch & Nạp Tiền</h2>
            <ul>
                <li><strong>Nạp ATM / VietQR:</strong> Hệ thống tự động xử lý và cộng tiền 24/7 thông qua cổng tích hợp ngân hàng. Quý khách vui lòng chuyển đúng cú pháp nội dung chuyển khoản được cấp trên màn hình hóa đơn. Mỗi mã QR chỉ có hiệu lực thanh toán trong vòng 15 phút.</li>
                <li><strong>Nạp Thẻ Cào:</strong> Áp dụng nạp tự động qua cổng API uy tín. Quý khách cần nhập chính xác nhà mạng, mã thẻ (PIN) và số seri. Trường hợp chọn sai mệnh giá sẽ được đối soát theo quy định cổng thanh toán.</li>
                <li><strong>Hoàn Tiền:</strong> Số dư đã nạp thành công vào hệ thống được sử dụng để thanh toán các dịch vụ và tài khoản trên Yukistore.</li>
            </ul>

            <h2>4. Quy Định Dịch Vụ Cày Thuê</h2>
            <ul>
                <li>Mỗi khách hàng chỉ được đặt tối đa 01 đơn cày thuê đang trong tiến trình xử lý (trạng thái chờ hoặc đang cày) cho đến khi kỹ thuật viên hoàn tất.</li>
                <li>Khách hàng được chọn tối đa 03 gói cày thuê đồng thời trên cùng một đơn dịch vụ.</li>
                <li>Khách hàng cam kết cung cấp chính xác thông tin tài khoản game và không đăng nhập vào game trong lúc kỹ thuật viên đang tiến hành cày thuê để tránh xung đột phiên đăng nhập.</li>
            </ul>

            <h2>5. Chính Sách Bảo Hành & Đổi Trả</h2>
            <ul>
                <li>Cam kết 100% tài khoản đúng mô tả và thông tin đăng tải.</li>
                <li>Hỗ trợ bảo hành 1 đổi 1 hoặc hoàn tiền nếu tài khoản lỗi thông tin, sai mật khẩu ngay tại thời điểm mua.</li>
                <li>Thời gian tiếp nhận và xử lý khiếu nại trong vòng 24 giờ kể từ khi phát sinh đơn hàng.</li>
            </ul>

            <h2>6. Quyền Sở Hữu & Bản Quyền</h2>
            <p>Toàn bộ thương hiệu, giao diện, logo, nội dung và mã nguồn thuộc quyền sở hữu của <strong>Yukistore</strong>. Nghiêm cấm mọi hành vi sao chép, giả mạo thương hiệu mà không có sự đồng ý bằng văn bản.</p>

            <h2>7. Thông Tin Liên Hệ Hỗ Trợ</h2>
            <p>Mọi thắc mắc hoặc yêu cầu hỗ trợ xin vui lòng liên hệ:</p>
            <ul>
                <li><strong>Hotline / Zalo:</strong> 0373992680</li>
                <li><strong>Fanpage Facebook:</strong> https://www.facebook.com/profile.php?id=61554102541296</li>
                <li><strong>Cộng đồng Discord:</strong> https://discord.gg/sPPEKffZrH</li>
            </ul>
        </div>';
    }

    protected function getDefaultPrivacyPolicy(): string
    {
        return '
        <div class="legal-doc">
            <h2>1. Mục Đích Thu Thập Dữ Liệu</h2>
            <p><strong>Yukistore</strong> cam kết tôn trọng và bảo vệ quyền riêng tư cá nhân của người dùng. Chính sách bảo mật này giải thích cách chúng tôi thu thập, sử dụng, lưu trữ và bảo vệ thông tin cá nhân của quý khách khi tham gia trải nghiệm dịch vụ trên hệ thống.</p>

            <h2>2. Phạm Vi Thông Tin Thu Thập</h2>
            <p>Các dữ liệu chúng tôi thu thập bao gồm:</p>
            <ul>
                <li><strong>Thông tin tài khoản:</strong> Tên đăng nhập, email, số điện thoại, ảnh đại diện do khách hàng cung cấp.</li>
                <li><strong>Thông tin giao dịch:</strong> Lịch sử nạp tiền, biến động số dư, lịch sử mua tài khoản và lịch sử đơn cày thuê.</li>
                <li><strong>Thông tin kỹ thuật:</strong> Địa chỉ IP, trình duyệt web, thời gian truy cập nhằm mục đích tăng cường an ninh và ngăn chặn gian lận.</li>
            </ul>

            <h2>3. Tiêu Chuẩn Bảo Mật Thông Tin</h2>
            <ul>
                <li>Mật khẩu người dùng được mã hóa bằng thuật toán một chiều an toàn (Bcrypt/Argon2) và tuyệt đối không thể giải mã ngược.</li>
                <li>Hệ thống máy chủ áp dụng kết nối mã hóa SSL/TLS 256-bit trong toàn bộ quá trình truyền tải dữ liệu.</li>
                <li>Tích hợp các cơ chế bảo vệ: Giới hạn tần suất đăng nhập (Rate Limiting), xác thực mã Captcha chống bot tự động.</li>
            </ul>

            <h2>4. Cam Kết Không Chia Sẻ Dữ Liệu</h2>
            <p>Yukistore cam kết không bán, không cho thuê và không chia sẻ thông tin cá nhân của quý khách cho bất kỳ bên thứ ba nào vì mục đích thương mại. Thông tin chỉ được cung cấp cho cơ quan nhà nước có thẩm quyền trong trường hợp có yêu cầu chính thức theo quy định của pháp luật Việt Nam.</p>

            <h2>5. Quyền Lợi Của Khách Hàng</h2>
            <ul>
                <li>Quý khách có toàn quyền truy cập, kiểm tra và cập nhật thông tin cá nhân của mình tại trang Quản lý hồ sơ.</li>
                <li>Có quyền yêu cầu hỗ trợ đóng tài khoản hoặc xóa bỏ dữ liệu khi không còn nhu cầu sử dụng.</li>
            </ul>

            <h2>6. Liên Hệ Phụ Trách An Toàn Thông Tin</h2>
            <p>Nếu quý khách có bất kỳ câu hỏi nào về chính sách bảo mật hoặc phát hiện lỗ hổng an ninh, xin vui lòng thông báo ngay cho đội ngũ quản trị qua Zalo: <strong>0373992680</strong> hoặc kênh Discord hỗ trợ 24/7.</p>
        </div>';
    }
}
