<?php

use App\Http\Controllers\Backend\Farhad\BrandController;
use App\Http\Controllers\Backend\Farhad\CategoryController;
use App\Http\Controllers\Backend\Farhad\DashboardController;
use App\Http\Controllers\Backend\Farhad\PackageController;
use App\Http\Controllers\Backend\Farhad\ProductController;
use App\Http\Controllers\Backend\Farhad\StatusController;
use App\Http\Controllers\Backend\Setting\AdminSettingController;
use App\Http\Controllers\Backend\Setting\MailSettingController;
use App\Http\Controllers\Backend\Setting\ManagerController;
use App\Http\Controllers\Backend\Setting\ProfileSettingController;
use App\Http\Controllers\Backend\Setting\SocialSettingController;
use App\Http\Controllers\Backend\Setting\StripeSettingController;
use App\Http\Controllers\Backend\Setting\SystemSettingController;
use Illuminate\Support\Facades\Route;


Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'es'])) {
        abort(400);
    }

    session(['locale' => $locale]);

    return redirect()->back();
});


Route::middleware(['auth:web', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard route
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Brands routes
    Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

    // Categories routes
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Products routes
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Profile settings routes
    Route::get('settings/profile', [ProfileSettingController::class, 'edit'])->name('profile-settings.edit');
    Route::post('settings/profile/{id}', [ProfileSettingController::class, 'update'])->name('profile-settings.update');
    Route::post('settings/profile/change-password', [ProfileSettingController::class, 'changePassword'])->name('profile-settings.change-password');

    // Manager management routes
    Route::get('settings/managers', [ManagerController::class, 'index'])->name('managers.index');
    Route::post('settings/managers', [ManagerController::class, 'store'])->name('managers.store');
    Route::put('settings/managers/{id}', [ManagerController::class, 'update'])->name('managers.update');
    Route::delete('settings/managers/{id}', [ManagerController::class, 'destroy'])->name('managers.destroy');

    // Social settings routes
    Route::get('settings/social', [SocialSettingController::class, 'edit'])->name('social-settings.edit');
    Route::post('settings/social', [SocialSettingController::class, 'update'])->name('social-settings.update');

    // Mail settings routes
    Route::get('settings/mail', [MailSettingController::class, 'edit'])->name('mail-settings.edit');
    Route::post('settings/mail', [MailSettingController::class, 'update'])->name('mail-settings.update');

    // Stripe Settings routes
    Route::get('settings/stripe', [StripeSettingController::class, 'edit'])->name('stripe-settings.edit');
    Route::post('settings/stripe', [StripeSettingController::class, 'update'])->name('stripe-settings.update');

    // System Settings routes
    Route::get('settings/system', [SystemSettingController::class, 'edit'])->name('system-settings.edit');
    Route::post('settings/system', [SystemSettingController::class, 'update'])->name('system-settings.update');

    // Systems routes
    Route::get('settings/admin', [AdminSettingController::class, 'edit'])->name('admin-settings.edit');
    Route::post('settings/admin', [AdminSettingController::class, 'update'])->name('admin-settings.update');

    //Status
    Route::post('/update-status', [StatusController::class, 'update'])->name('status.update');

    // Packages routes
    Route::get('packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::put('packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
});
