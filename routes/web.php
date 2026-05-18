<?php

use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [App\Http\Controllers\Auth\LoginController::class,    'showLoginForm'])->name('login');
    Route::post('/login',   [App\Http\Controllers\Auth\LoginController::class,    'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',[App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

Route::post('/logout', [App\Http\Controllers\Auth\LogoutController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Public / Customer ─────────────────────────────────────────────────────────
Route::get('/', [App\Http\Controllers\Customer\HomeController::class, 'index'])->name('home');

Route::prefix('menu')->name('menu.')->group(function () {
    Route::get('/',         [App\Http\Controllers\Customer\MenuController::class, 'index'])->name('index');
    Route::get('/{slug}',   [App\Http\Controllers\Customer\MenuController::class, 'show'])->name('show');
});

// ── Authenticated Customer ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:customer', 'check.banned'])->group(function () {

    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/',              [App\Http\Controllers\Customer\CartController::class, 'index'])->name('index');
        Route::post('/add',          [App\Http\Controllers\Customer\CartController::class, 'add'])->name('add');
        Route::put('/update/{id}',   [App\Http\Controllers\Customer\CartController::class, 'update'])->name('update');
        Route::delete('/remove/{id}',[App\Http\Controllers\Customer\CartController::class, 'remove'])->name('remove');
        Route::get('/count',         [App\Http\Controllers\Customer\CartController::class, 'count'])->name('count');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',                  [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('index');
        Route::get('/checkout',          [App\Http\Controllers\Customer\OrderController::class, 'checkout'])->name('checkout');
        Route::post('/',                 [App\Http\Controllers\Customer\OrderController::class, 'store'])->name('store');
        Route::get('/{id}',              [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('show');
        Route::get('/{id}/tracking',     [App\Http\Controllers\Customer\OrderController::class, 'tracking'])->name('tracking');
        Route::post('/{id}/complete',    [App\Http\Controllers\Customer\OrderController::class, 'complete'])->name('complete');
        Route::get('/{id}/status-poll',  [App\Http\Controllers\Customer\OrderController::class, 'statusPoll'])->name('status-poll');
        Route::post('/check-voucher',    [App\Http\Controllers\Customer\OrderController::class, 'checkVoucher'])->name('check-voucher');
    });

    // Wallet
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/',         [App\Http\Controllers\Customer\WalletController::class, 'index'])->name('index');
        Route::get('/topup',    [App\Http\Controllers\Customer\WalletController::class, 'topupForm'])->name('topup');
        Route::post('/topup',   [App\Http\Controllers\Customer\WalletController::class, 'topup'])->name('topup.store');
    });

    // Reservation
    Route::prefix('reservasi')->name('reservation.')->group(function () {
        Route::get('/',         [App\Http\Controllers\Customer\ReservationController::class, 'index'])->name('index');
        Route::get('/buat',     [App\Http\Controllers\Customer\ReservationController::class, 'create'])->name('create');
        Route::post('/',        [App\Http\Controllers\Customer\ReservationController::class, 'store'])->name('store');
        Route::delete('/{id}',  [App\Http\Controllers\Customer\ReservationController::class, 'cancel'])->name('cancel');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',         [App\Http\Controllers\Customer\ProfileController::class, 'index'])->name('index');
        Route::put('/',         [App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('update');
        Route::post('/avatar',  [App\Http\Controllers\Customer\ProfileController::class, 'updateAvatar'])->name('avatar');
        Route::put('/password', [App\Http\Controllers\Customer\ProfileController::class, 'updatePassword'])->name('password');
        Route::put('/darkmode', [App\Http\Controllers\Customer\ProfileController::class, 'toggleDarkMode'])->name('darkmode');
    });

    // Membership
    Route::get('/membership', [App\Http\Controllers\Customer\MembershipController::class, 'index'])->name('membership');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',             [App\Http\Controllers\Customer\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read',   [App\Http\Controllers\Customer\NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all',    [App\Http\Controllers\Customer\NotificationController::class, 'markAllRead'])->name('read-all');
        Route::get('/count',        [App\Http\Controllers\Customer\NotificationController::class, 'count'])->name('count');
    });

    // Favorite toggle
    Route::post('/menu/{id}/favorite', [App\Http\Controllers\Customer\MenuController::class, 'toggleFavorite'])->name('menu.favorite');
});

// ── Kasir ─────────────────────────────────────────────────────────────────────
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir,superadmin', 'check.banned'])->group(function () {
    Route::get('/',                       [App\Http\Controllers\Kasir\KasirController::class,       'dashboard'])->name('dashboard');
    Route::get('/orders',                 [App\Http\Controllers\Kasir\OrderManageController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}',            [App\Http\Controllers\Kasir\OrderManageController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status',    [App\Http\Controllers\Kasir\OrderManageController::class, 'updateStatus'])->name('orders.status');
    Route::get('/orders/poll',            [App\Http\Controllers\Kasir\OrderManageController::class, 'poll'])->name('orders.poll');
});

// ── Kitchen ───────────────────────────────────────────────────────────────────
Route::prefix('kitchen')->name('kitchen.')->middleware(['auth', 'role:kitchen,superadmin', 'check.banned'])->group(function () {
    Route::get('/',                    [App\Http\Controllers\Kitchen\KitchenController::class, 'display'])->name('display');
    Route::get('/orders',              [App\Http\Controllers\Kitchen\KitchenController::class, 'orders'])->name('orders');
    Route::post('/orders/{id}/done',   [App\Http\Controllers\Kitchen\KitchenController::class, 'markDone'])->name('orders.done');
});

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:superadmin', 'check.banned'])->group(function () {

    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Menu management
    Route::resource('menus', App\Http\Controllers\Admin\MenuController::class)->except(['show']);
    Route::post('/menus/{id}/toggle',   [App\Http\Controllers\Admin\MenuController::class, 'toggle'])->name('menus.toggle');
    Route::post('/menus/upload-image',  [App\Http\Controllers\Admin\MenuController::class, 'uploadImage'])->name('menus.upload-image');

    // Users
    Route::get('/users',                [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}',           [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/suspend',  [App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{id}/activate', [App\Http\Controllers\Admin\UserController::class, 'activate'])->name('users.activate');

    // Orders
    Route::get('/orders',               [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}',          [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel',  [App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');

    // Wallet
    Route::get('/wallet',                          [App\Http\Controllers\Admin\WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/topup-requests',           [App\Http\Controllers\Admin\WalletController::class, 'topupRequests'])->name('wallet.topup-requests');
    Route::post('/wallet/topup/{id}/approve',      [App\Http\Controllers\Admin\WalletController::class, 'approve'])->name('wallet.approve');
    Route::post('/wallet/topup/{id}/reject',       [App\Http\Controllers\Admin\WalletController::class, 'reject'])->name('wallet.reject');

    // Membership
    Route::get('/membership',                      [App\Http\Controllers\Admin\MembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/{id}/approve-platinum',[App\Http\Controllers\Admin\MembershipController::class, 'approvePlatinum'])->name('membership.approve-platinum');

    // Banners
    Route::resource('banners', App\Http\Controllers\Admin\BannerController::class)->except(['show']);

    // Vouchers
    Route::resource('vouchers', App\Http\Controllers\Admin\VoucherController::class)->except(['show']);
    Route::post('/vouchers/{id}/toggle', [App\Http\Controllers\Admin\VoucherController::class, 'toggle'])->name('vouchers.toggle');

    // Reservations
    Route::get('/reservations',                    [App\Http\Controllers\Admin\ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/{id}/approve',      [App\Http\Controllers\Admin\ReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservations/{id}/reject',       [App\Http\Controllers\Admin\ReservationController::class, 'reject'])->name('reservations.reject');

    // Settings
    Route::get('/settings',   [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings',   [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Analytics
    Route::get('/analytics',              [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data',         [App\Http\Controllers\Admin\AnalyticsController::class, 'data'])->name('analytics.data');

    // Notifications broadcast
    Route::post('/notifications/broadcast', [App\Http\Controllers\Admin\NotificationController::class, 'broadcast'])->name('notifications.broadcast');
});

// ── API / Chatbot ─────────────────────────────────────────────────────────────
Route::prefix('api')->group(function () {
    Route::post('/chatbot', [App\Http\Controllers\Api\ChatbotController::class, 'respond'])->name('chatbot.respond');
});