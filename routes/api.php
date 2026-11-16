<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CardDepositController;
use App\Http\Controllers\DiscountCodeController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Callback thẻ cào từ partner — công khai (partner gọi server-to-server), đã có sign md5 riêng.
// Thêm throttle để chống abuse.
Route::match(['GET', 'POST'],'/callback/card', [CardDepositController::class, 'handleCallback'])
    ->middleware('throttle:20,1')->name('callback.card');

// Discount code validation
Route::post('/discount-codes/validate', [DiscountCodeController::class, 'validateCode'])
    ->middleware('throttle:30,1');

// Lệnh quét giao dịch ngân hàng — CHỈ admin đăng nhập (session web) mới được kích hoạt tay.
// Trong môi trường production nên chạy bằng cron: * * * * * php artisan fetch:mb-transactions
Route::get('/auto-bank-deposit', function () {
    Artisan::call('fetch:mb-transactions');
    return response()->json(['success' => true, 'message' => 'Đã quét giao dịch.']);
})->middleware(['auth', 'admin', 'throttle:3,1']);

// Danh sách phương thức thanh toán đang bật — cho frontend render động.
// KHÔNG BAO GIỜ trả trường config (chứa secret) ra đây.
Route::get('/payment-methods', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\PaymentMethod::query()
            ->enabled()
            ->get()
            ->map(fn ($m) => $m->toPublicArray())
            ->values(),
    ]);
})->middleware('throttle:60,1');