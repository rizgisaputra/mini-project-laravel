<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HeaderCustomerCartController;
use App\Http\Controllers\HeaderDetailCustomerCartController;
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
    Route::get('products', [ProductController::class,'index'])->name('product.index');
    Route::post('/products', [ProductController::class, 'store'])->name('product.store');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categorie.index');
    Route::get('/categories/{id}', [CategoryController::class,'show'])->name('categories.show');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::post('/sellers', [AuthController::class, 'addSeller'])->name('sellers.addSeller');
    Route::put('/products-categories/{id}', [ProductCategory::class, 'updateCategoryInProductCategories'])
    ->name('products_categories.updateCategoryInProductCategories');

    Route::post('/headers-details', [HeaderDetailCustomerCartController::class, 'store'])->name('headers-details.store');
    Route::get('/headers-details', [HeaderDetailCustomerCartController::class, 'index'])->name('headers-details.index');
    Route::get('/headers-details/{id}', [HeaderDetailCustomerCartController::class, 'show'])->name('headers-details.show');
    Route::put('/headers-details/{id}', [HeaderDetailCustomerCartController::class, 'update'])->name('headers-details.update');
    Route::delete('/headers-details/{id}', [HeaderDetailCustomerCartController::class, 'destroy'])->name('headers-datail.destroy');

    Route::get('/headers', [HeaderCustomerCartController::class, 'index'])->name('headers.index');
    Route::get('/headers/{id}', [HeaderCustomerCartController::class, 'show'])->name('headers.show');
});

