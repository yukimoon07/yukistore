<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GameAccount;
use App\Models\GameCategory;
use App\Models\RandomCategory;
use App\Models\RandomCategoryAccount;
use Illuminate\Http\Request;

class GameCategoryController extends Controller
{
    public function index(string $slug, Request $request)
    {
        $category = GameCategory::where("slug", $slug)->firstOrFail();

        // Get all accounts linked to this category
        $accounts = GameAccount::where('game_category_id', $category->id);
        if (!$request->filled('status')) {
            $accounts->where('status', 'available');
        }

        // Base queries
        if ($request->hasAny(['code', 'price_range', 'status', 'price_from', 'price_to'])) {
            if ($request->filled('code')) {
                $accounts->where('id', $request->code);
            }
            if ($request->filled('price_range')) {
                $range = explode('-', $request->price_range);
                if (count($range) == 2) {
                    $accounts->whereBetween('price', $range);
                } else {
                    $accounts->where('price', '>=', $range[0]);
                }
            }
            // Form filter dùng price_from/price_to — trước đây bị bỏ qua nên lọc giá không có tác dụng.
            if ($request->filled('price_from')) {
                $accounts->where('price', '>=', (float) $request->price_from);
            }
            if ($request->filled('price_to')) {
                $accounts->where('price', '<=', (float) $request->price_to);
            }
            if ($request->filled('status')) {
                $accounts->where('status', $request->status);
            }
        }

        $sort = $request->input('sort');
        $accounts = match ($sort) {
            'price_asc'  => $accounts->orderBy('price', 'ASC')->orderBy('id', 'DESC'),
            'price_desc' => $accounts->orderBy('price', 'DESC')->orderBy('id', 'DESC'),
            'oldest'     => $accounts->orderBy('id', 'ASC'),
            default      => $accounts->orderBy('id', 'DESC'),
        };
        $accounts = $accounts->get();

        $flashSalePrice = \App\Models\FlashSale::getActivePrice('game', $category->id);
        if ($flashSalePrice !== null) {
            $accounts->each(function($acc) use ($flashSalePrice) {
                $acc->price = $flashSalePrice;
            });
        }

        // Collect dynamic keys
        $dynamicKeys = [];
        foreach ($accounts as $acc) {
            $details = is_array($acc->details) ? $acc->details : json_decode($acc->details, true) ?? [];
            foreach ($details as $detail) {
                if (isset($detail['key']) && !in_array($detail['key'], $dynamicKeys)) {
                    $dynamicKeys[] = $detail['key'];
                }
            }
        }

        // Apply dynamic detail filters via Collection
        if ($request->has('details') && is_array($request->details)) {
            $accounts = $accounts->filter(function ($account) use ($request) {
                $accDetails = is_array($account->details) ? $account->details : json_decode($account->details, true) ?? [];
                
                foreach ($request->details as $filterKey => $filterValue) {
                    if (empty($filterValue)) continue;
                    
                    $foundMatch = false;
                    foreach ($accDetails as $detail) {
                        if (isset($detail['key']) && $detail['key'] === $filterKey) {
                            // Case-insensitive partial match
                            if (stripos((string)($detail['value'] ?? ''), (string)$filterValue) !== false) {
                                $foundMatch = true;
                                break;
                            }
                        }
                    }
                    if (!$foundMatch) {
                        return false; // This account doesn't match this filter
                    }
                }
                return true;
            });
        }

        // Paginate the collection manually
        $perPage = 12;
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $paginatedAccounts = new \Illuminate\Pagination\LengthAwarePaginator(
            $accounts->forPage($page, $perPage),
            $accounts->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
        $accounts = $paginatedAccounts;

        return view('user.category.show', compact('category', 'accounts', 'dynamicKeys'));
    }

    public function showAll()
    {
        $title = 'Danh mục bán nick game';
        $gameGroups = \App\Models\GameGroup::where('active', true)->orderBy('order', 'asc')->orderBy('id', 'asc')->get();

        // Get all categories with additional statistics
        $categories = Category::with('gameGroup')->where('active', true)->get();

        foreach ($categories as $category) {
            // Total accounts in this category
            $category->allAccount = GameAccount::where('game_category_id', $category->id)->where('status', 'available')->count();

            // Sold accounts in this category
            $category->soldCount = GameAccount::where('game_category_id', $category->id)
                ->where('status', 'sold')
                ->count();
            $category->price = GameAccount::where('game_category_id', $category->id)
                ->where('status', 'available')
                ->min('price') ?: 0;
            $category->url = route('category.index', ['slug' => $category->slug]);
        }

        $randomCategories = RandomCategory::with('gameGroup')->where('active', true)->get();
        foreach ($randomCategories as $category) {
            $category->soldCount = RandomCategoryAccount::where('random_category_id', $category->id)
                ->where('status', 'sold')
                ->count();
            $category->allAccount = RandomCategoryAccount::where('random_category_id', $category->id)->where('status', 'available')->count();
            $category->price = RandomCategoryAccount::where('random_category_id', $category->id)
                ->where('status', 'available')
                ->value('price') ?: 0;
            $category->url = route('random.index', ['slug' => $category->slug]);
        }

        $categories = $categories->concat($randomCategories);

        return view('user.category.show-all', compact('categories', 'title', 'gameGroups'));
    }

    public function showGroup($slug)
    {
        $gameGroup = \App\Models\GameGroup::where('slug', $slug)->first();
        if (!$gameGroup) {
            $gameGroup = \App\Models\GameGroup::all()->first(function($g) use ($slug) {
                return \Illuminate\Support\Str::slug($g->name) === $slug;
            });
        }
        if (!$gameGroup) {
            abort(404);
        }
        $title = $gameGroup->name;
        
        $categories = Category::where(function($q) use ($gameGroup) {
            $q->where('game_group_id', $gameGroup->id)
              ->orWhere('platform', $gameGroup->name);
        })->where('active', true)->get();

        foreach ($categories as $category) {
            $category->allAccount = GameAccount::where('game_category_id', $category->id)->where('status', 'available')->count();
            $category->soldCount = GameAccount::where('game_category_id', $category->id)->where('status', 'sold')->count();
            $category->price = GameAccount::where('game_category_id', $category->id)
                ->where('status', 'available')
                ->min('price') ?: 0;
            $category->url = route('category.index', ['slug' => $category->slug]);
        }

        $randomCategories = RandomCategory::where(function($q) use ($gameGroup) {
            $q->where('game_group_id', $gameGroup->id)
              ->orWhere('platform', $gameGroup->name);
        })->where('active', true)->get();
        foreach ($randomCategories as $category) {
            $category->soldCount = RandomCategoryAccount::where('random_category_id', $category->id)->where('status', 'sold')->count();
            $category->allAccount = RandomCategoryAccount::where('random_category_id', $category->id)->where('status', 'available')->count();
            $category->price = RandomCategoryAccount::where('random_category_id', $category->id)
                ->where('status', 'available')
                ->value('price') ?: 0;
            $category->url = route('random.index', ['slug' => $category->slug]);
        }

        $categories = $categories->concat($randomCategories);
        $platform = $gameGroup->name;

        return view('user.category.show-group', compact('categories', 'title', 'gameGroup', 'platform'));
    }
}
