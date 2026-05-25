<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Pelanggan\StoreController;
use App\Http\Controllers\Pelanggan\CartController;
use App\Http\Controllers\Pelanggan\OrderController as PelangganOrderController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role === 'owner')
        return redirect()->route('owner.dashboard');
    if ($role === 'admin')
        return redirect()->route('admin.dashboard');
    return redirect()->route('pelanggan.store');
})->middleware(['auth', 'verified'])->name('dashboard');

use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;

// OWNER ROUTES
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('ingredients', \App\Http\Controllers\Owner\IngredientController::class);
    Route::post('/ingredients/{ingredient}/transfer', [\App\Http\Controllers\Owner\IngredientController::class, 'transfer'])->name('ingredients.transfer');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Owner\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Owner\ReportController::class, 'exportCsv'])->name('reports.export');
    Route::get('/recipes', [\App\Http\Controllers\Admin\RecipeController::class, 'index'])->name('recipes.index');
});

// ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminOrderController::class, 'index'])->name('dashboard');
    Route::get('/orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print');
    Route::resource('orders', AdminOrderController::class)->only(['index', 'update', 'show']);
    Route::resource('ingredients', \App\Http\Controllers\Owner\IngredientController::class);
    Route::post('/ingredients/{ingredient}/transfer', [\App\Http\Controllers\Owner\IngredientController::class, 'transfer'])->name('ingredients.transfer');
    Route::get('/recipes', [\App\Http\Controllers\Admin\RecipeController::class, 'index'])->name('recipes.index');
});

// PELANGGAN ROUTES
Route::middleware(['auth', 'role:pelanggan'])->prefix('store')->name('pelanggan.')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('store');
    Route::get('/category/{category}', [StoreController::class, 'category'])->name('category');
    Route::get('/product/{product}', [StoreController::class, 'product'])->name('product');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/decrement/{product}', [CartController::class, 'decrement'])->name('cart.decrement');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::post('/checkout', [PelangganOrderController::class, 'store'])->name('checkout');
    Route::get('/orders', [PelangganOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [PelangganOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/status', [PelangganOrderController::class, 'status'])->name('orders.status');
    Route::post('/orders/{order}/upload-proof', [PelangganOrderController::class, 'uploadProof'])->name('orders.upload_proof');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
