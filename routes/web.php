<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');

Route::post('/login', [\App\Http\Controllers\LoginController::class, 'authenticate'])->name('login.post');

Route::post('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/categories', function () {
            return view('admin.categories');
        });
        Route::get('/products', function () {
            return view('admin.products');
        });

        Route::get('/account', function () {
            return view('admin.account');
        })->name('admin.account');

        Route::get('/users', function () {
            return view('admin.users');
        })->name('admin.users');

        Route::get('/incomes', function () {
            return view('admin.incomes');
        })->name('admin.incomes');

        Route::get('/expenses', function () {
            return view('admin.expenses');
        })->name('admin.expenses');

        Route::get('/reports', [\App\Http\Controllers\Api\Admin\ReportController::class, 'index'])->name('admin.reports');
        Route::post('/reports/export', [\App\Http\Controllers\Api\Admin\ReportController::class, 'export'])->name('admin.reports.export');

        // JSON API endpoints for Datatables/Fetch (Session Authenticated)
        Route::apiResource('api/categories', \App\Http\Controllers\Api\Admin\CategoryController::class);
        Route::apiResource('api/products', \App\Http\Controllers\Api\Admin\ProductController::class);
        Route::apiResource('api/users', \App\Http\Controllers\Api\Admin\UserController::class);
        Route::apiResource('api/expenses', \App\Http\Controllers\Api\Admin\ExpenseController::class);

        // Account Profile API
        Route::post('api/account', [\App\Http\Controllers\Api\Admin\ProfileController::class, 'update'])->name('admin.profile.update');
    });

    Route::get('/cashier', function () {
        return view('cashier.dashboard');
    });

    // Cashier/Web API Routes (Session Authenticated)
    Route::prefix('cashier-api')->group(function () {
        Route::get('/user', function (Illuminate\Http\Request $request) {
            return $request->user();
        });

        // Read-only access for cashier interface
        Route::get('/categories', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'index']);
        Route::get('/products', [\App\Http\Controllers\Api\Admin\ProductController::class, 'index']);
        Route::post('/transactions', [\App\Http\Controllers\Api\Cashier\TransactionController::class, 'store']);
        Route::get('/transactions', [\App\Http\Controllers\Api\Cashier\TransactionController::class, 'index']);
        // Note: Using Admin Controllers for read access is fine if logic is standard
    });
    // Route Struk & WhatsApp
    Route::get('/cashier/struk/{id}', [\App\Http\Controllers\Api\Cashier\StrukController::class, 'index'])->name('cashier.struk');
    Route::post('/cashier/send-whatsapp', [\App\Http\Controllers\Api\Cashier\StrukController::class, 'sendWhatsapp']);
});
