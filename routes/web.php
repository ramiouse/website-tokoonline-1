<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;

use App\Http\Controllers as GeneralController;
use App\Http\Controllers\Admin as AdminController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// default first
Route::get('/', [GeneralController\HomeController::class,"index"])->name('home');

// Categories
Route::get('/categories', [GeneralController\CategoryController::class,"index"])->name('categories');
Route::get('/categories/{id}', [GeneralController\CategoryController::class,"detail"])->name('categories-detail');

// Details
Route::get('/details/{id}', [GeneralController\DetailController::class,"index"])->name('detail');
Route::post('/details/{id}', [GeneralController\DetailController::class,"add"])->name('detail-add');

// Midtrans/payment routes
Route::post('/checkout/callback', [GeneralController\CheckoutController::class,"callback"])->name('midtrans-callback');

// Success
Route::get('/success', [GeneralController\CartController::class,"success"])->name('success');
Route::get('/register/success', [GeneralController\Auth\RegisterController::class,"success"])->name('register-success');

// with middleware : auth
Route::middleware(['auth'])->group(function() {
    // Cart
    Route::get('/cart', [GeneralController\CartController::class,"index"])
    ->name('cart');
    Route::delete('/cart/{id}', [GeneralController\CartController::class,"delete"])
    ->name('cart-delete');

    // Checkout
    Route::post('/checkout', [GeneralController\CheckoutController::class,"process"])
    ->name('checkout');

    // products
    Route::get('/dashboard', [GeneralController\DashboardController::class,"index"])->name('dashboard');
    Route::get('/dashboard/products', [GeneralController\DashboardProductController::class,"index"])->name('dashboard-product');
    Route::get('/dashboard/products/{id}', [GeneralController\DashboardProductController::class,"details"])
            ->whereNumber('id')
            ->name('dashboard-product-details');

    Route::post('/dashboard/products/{id}', [GeneralController\DashboardProductController::class,"update"])
    ->whereNumber('id')
    ->name('dashboard-product-update');

    Route::get('/dashboard/products/create', [GeneralController\DashboardProductController::class,"create"])->name('dashboard-product-create');
    Route::post('/dashboard/products', [GeneralController\DashboardProductController::class,"store"])->name('dashboard-product-store');
   
    // galleries
    Route::post('/dashboard/products/gallery/upload', [GeneralController\DashboardProductController::class,"uploadGallery"])
    ->name('dashboard-product-gallery-upload');
    Route::get('/dashboard/products/gallery/delete/{id}', [GeneralController\DashboardProductController::class,"deleteGallery"])
    ->name('dashboard-product-gallery-delete');

    // transactions
    Route::get('/dashboard/transactions', [GeneralController\DashboardTransactionController::class,"index"])
    ->name('dashboard-transaction');
    Route::get('/dashboard/transactions/{id}', [GeneralController\DashboardTransactionController::class,"details"])
    ->name('dashboard-transaction-details');
    Route::post('/dashboard/transactions/{id}', [GeneralController\DashboardTransactionController::class,"update"])
    ->name('dashboard-transaction-update');

    // settings
    Route::get('/dashboard/settings', [GeneralController\DashboardSettingController::class,"store"])
    ->name('dashboard-settings-store');
    
    Route::get('/dashboard/account', [GeneralController\DashboardSettingController::class,"account"])
    ->name('dashboard-settings-account');
    
    Route::post('/dashboard/update/{redirect}', [GeneralController\DashboardSettingController::class,"update"])
    ->name('dashboard-settings-redirect');
});

// admin section
// no need namespace for laravel 8.0 >
Route::prefix('admin')
->name('admin.')
->middleware(['auth','admin'])
->group(function(){
    Route::get('/', [AdminController\DashboardController::class,"index"])->name('admin-dashboard');
    Route::resource('category', AdminController\CategoryController::class);
    Route::resource('user', AdminController\UserController::class);
    Route::resource('product', AdminController\ProductController::class);
    Route::resource('product-gallery', AdminController\ProductGalleryController::class);
    // Route::resource('transaction', AdminController\TransactionController::class);
});

Auth::routes();

