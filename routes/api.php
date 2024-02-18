<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerCartController;
use App\Http\Controllers\HeaderCustomerCartController;
use App\Http\Controllers\HeaderDetailCustomerCartController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProductController;
use App\Models\HeaderCustomerCart;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/register',[AuthController::class,'register'])->name('register');
Route::post('/login',[AuthController::class,'login'])->name('login');

Route::middleware(['auth:api'])->group( function (){
    Route::get('/products', [ProductController::class,'index'])
    ->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])
    ->name('products.store');
    Route::get('/products/{id}', [ProductController::class, 'show'])
    ->name('products.show');
    Route::put('/products/{id}', [ProductController::class, 'update'])
    ->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])
    ->name('products.destroy');

    Route::get('/products/admin/customer', [ProductController::class,'indexForAdminAndCustomer'])
    ->name('products-admin-customer.index');
    Route::post('/products/admin', [ProductController::class, 'storeForAdmin'])
    ->name('products-admin.store');
    Route::get('/products/admin/customer/{id}', [ProductController::class, 'showForAdminAndCustomer'])
    ->name('products-admin-customer.show');
    Route::put('/products/admin/{id}', [ProductController::class, 'updateForAdmin'])
    ->name('products-admin.update');
    Route::delete('/products/admin/{id}', [ProductController::class, 'destroyForAdmin'])
    ->name('products-admin.destroy');

    Route::get('/users', [AdminController::class, 'index'])
    ->name('categorie.index');
    Route::get('/users/{id}', [AdminController::class,'show'])
    ->name('users.show');
    Route::post('/users', [AdminController::class, 'store'])
    ->name('users.store');

    Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categorie.index');
    Route::get('/categories/{id}', [CategoryController::class,'show'])
    ->name('categories.show');
    Route::post('/categories', [CategoryController::class, 'store'])
    ->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])
    ->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
    ->name('categories.destroy');

    Route::post('/sellers', [AuthController::class, 'addSeller']
    )->name('sellers.addSeller');

    Route::get('/customers/carts/admin', [CustomerCartController::class, 'indexForAdmin'])
    ->name('customers-carts-admin.index');
    Route::get('/customers/carts/admin/{id}', [CustomerCartController::class, 'showForAdmin'])
    ->name('customers-carts-admin.show');
    Route::post('/customers/carts/admin', [CustomerCartController::class, 'storeForAdmin'])
    ->name('customers-carts-admin.store');
    Route::put('/customers/carts/admin/{id}', [CustomerCartController::class, 'updateForAdmin'])
    ->name('customers-carts-admin.update');
    Route::delete('/customers/carts/admin/{id}', [CustomerCartController::class, 'destroyForAdmin'])
    ->name('customers-carts-admin.destroy');

    Route::get('/customers/carts', [CustomerCartController::class, 'index'])
    ->name('customers-carts.index');
    Route::get('/customers/carts/{id}', [CustomerCartController::class, 'show'])
    ->name('customers-carts.show');
    Route::post('/customers/carts', [CustomerCartController::class, 'store'])
    ->name('customers-carts.store');
    Route::put('/customers/carts/{id}', [CustomerCartController::class, 'update'])
    ->name('customers-carts.update');
    Route::delete('/customers/carts/{id}', [CustomerCartController::class, 'destroy'])
    ->name('customers-carts.destroy');
    Route::delete('/checkout/cart', [CustomerCartController::class,'checkoutFromCart'])
    ->name('chcekoutCart.checkoutFromCart');
    Route::post('/checkout', [CustomerCartController::class,'checkout'])
    ->name('chcekout.checkout');

    Route::get('/header/admin', [HistoryController::class,'showHeaderForAdmin'])
    ->name('header-admin.showHeaderForAdmin');
    Route::get('/header-detail/admin/{id}', [HistoryController::class,'showHeaderDetailForAdmin'])
    ->name('header-detail-admin.showHeaderDetailForAdmin');
    Route::get('/header/customer', [HistoryController::class,'showHeaderForCustomer'])
    ->name('header-customer.showHeaderForCustomer');
    Route::get('/header-detail/customer/{id}', [HistoryController::class,'showHeaderDetailForCustomer'])
    ->name('header-detail-customer.showHeaderDetailForCustomer');
    Route::get('/header-detail-seller', [HistoryController::class,'showHistoryForSeller'])
    ->name('header-detail-seller.showHistoryForSeller');
});

