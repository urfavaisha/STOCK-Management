<?php
namespace App\Http\Controllers;
use App\Mail\sendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductOrderController;

Route::get('/changeLocale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        session()->put('locale', $locale);
        app()->setLocale($locale);
        return redirect()->back()->with('success', 'Language changed successfully');
    }
    return redirect()->back()->with('error', 'Invalid language selection');
})->name('changeLocale');

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/customers', [DashboardController::class, 'customers'])->name('customers.index');
    Route::get('/suppliers', [DashboardController::class, 'suppliers'])->name('suppliers.index');

    // Product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/api/products/{product}', [ProductController::class, 'show'])->name('api.products.show');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products-by-category', [CategoryController::class, 'productsByCategory'])->name('products.by.category');
    Route::get('/products-by-category/{category}', [CategoryController::class, 'getProductsByCategory'])->name('products.filter.by.category');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');

    // Products by Supplier routes
    Route::get('/products-by-supplier', [DashboardController::class, 'productsBySupplier'])->name('products.by.supplier');
    Route::get('/api/products-by-supplier/{supplier}', [DashboardController::class, 'getProductsBySupplier'])->name('api.products.by.supplier');

    // Products by Store routes
    Route::get('/products-by-store', [DashboardController::class, 'productsByStore'])->name('products.by.store');
    Route::get('/api/products-by-store/{store}', [DashboardController::class, 'getProductsByStore'])->name('api.products.by.store');

    // Order routes
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders.index');

    // Dashboard advanced reports routes
    Route::get('/customer-orders', [DashboardController::class, 'customerOrders'])->name('dashboard.customer_orders');
    Route::get('/suppliers-by-customer', [DashboardController::class, 'suppliersByCustomer'])->name('dashboard.suppliers_by_customer');
    Route::get('/products-same-warehouse', [DashboardController::class, 'productsSameWarehouse'])->name('dashboard.products_same_warehouse');
    Route::get('/products-per-warehouse', [DashboardController::class, 'productsPerWarehouse'])->name('dashboard.products_per_warehouse');
    Route::get('/warehouse-values', [DashboardController::class, 'warehouseValues'])->name('dashboard.warehouse_values');
    Route::get('/dashboard/warehouses-greater-value', [DashboardController::class, 'warehousesGreaterValue'])->name('dashboard.warehouses.greater.value');

    // Customer routes
    //Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::get('/customers/{customer}/delete', [CustomerController::class, 'delete'])->name('customers.delete');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Customer search API route
    Route::get('/api/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    // Customer search API route
    Route::get('/api/customers/search/{term}', [CustomerController::class, 'searchTerm'])->name('customers.search.term');

    // Customer orders API route
    Route::get('/api/customers/{customer}/orders', [OrderController::class, 'getCustomerOrders'])->name('customers.orders');

    // Order details route
    Route::get('/api/orders/{order}/details', [OrderController::class, 'getOrderDetails'])->name('orders.details');

    Route::get('/ordered-products', [ProductOrderController::class, 'index'])->name('ordered.products');
    Route::get('/same-products-customers', [CustomerController::class, 'sameProductsCustomers'])->name('same.products.customers');
    Route::get('products/orders-count', [ProductController::class, 'ordersCount'])->name('products.orders_count');
    Route::get('/products-more-than-6-orders', [ProductController::class, 'productsMoreThan6Orders'])->name('products.more_than_6_orders');
    Route::get('/order-totals', [OrderController::class, 'orderTotals'])->name('orders.totals');
    Route::get('/orders-greater-than-60', [OrderController::class, 'ordersGreaterThanOrder60'])->name('orders.greater_than_60');

    #Email :
    Route::view('/mail-form', 'mail.form')->name('mail.form');
    Route::post('/sendmail', function (Request $request) {
        $request->validate([
            'nom' => 'required|string',
            'email' => 'required|email',
            'sujet' => 'required|string',
        ]);

        $nom = $request->nom ;
        $sujet = $request->sujet;

        Mail::to($request->email)->send(new sendEmail($nom, $sujet));

        return back()->with('success', 'Email envoyé avec succès.');
    });

    #cookies & session & avatar
    Route::get('/cooksess', [DashboardController::class, 'cooksess']);
    Route::post("/saveCookie", [DashboardController::class, 'saveCookie'])->name("saveCookie");
    Route::post("/saveSession", [DashboardController::class, 'saveSession'])->name("saveSession");
    Route::post("/saveAvatar", [DashboardController::class, 'saveAvatar'])->name("saveAvatar");

    #chart
    Route::get('/chart', [DashboardController::class, 'chart'])->name('chart');

    ## excel
    Route::get('products-export', [ProductController::class, 'export'])->name('products.export');
    Route::post('products-import', [ProductController::class, 'import'])->name('products.import');
});
