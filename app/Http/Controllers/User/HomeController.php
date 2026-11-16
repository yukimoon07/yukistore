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

        return view('user.home', compact(
            'gameGroups',
            'categories',
            'services',
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
        $title = "Điều Khoản Sử Dụng";
        $content = \App\Support\HtmlSanitizer::clean(config_get('terms_of_use')) ?: '<p>Nội dung đang được cập nhật...</p>';
        return view('user.page', compact('title', 'content'));
    }

    public function privacy()
    {
        $title = "Chính Sách Bảo Mật";
        $content = \App\Support\HtmlSanitizer::clean(config_get('privacy_policy')) ?: '<p>Nội dung đang được cập nhật...</p>';
        return view('user.page', compact('title', 'content'));
    }
}
