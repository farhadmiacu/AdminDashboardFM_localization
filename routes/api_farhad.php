<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\ProfileController;

Route::middleware(['setLang'])->group(function () {
    // Public routes or lang specific
});

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/login', [AuthController::class, 'login']);

// Forgot Password for customer
Route::post('/password/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/password/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

//Profile Settings both Customer and Driver/Deliveryman
Route::middleware('auth:api')->group(function () {

    // language toggle update
    Route::post('/language-toggle', [ProfileController::class, 'toggleLanguage']);

    // Profile routes
    Route::get('/profile-info', [ProfileController::class, 'profileInfo']);
    Route::post('/profile-update', [ProfileController::class, 'profileUpdate']);
    Route::post('/profile-change-password', [ProfileController::class, 'changePassword']);
    Route::post('/profile-change-address', [ProfileController::class, 'changeAddress']);
    Route::post('/profile-delete', [ProfileController::class, 'profileDelete']);
    Route::post('/profile-update-location', [ProfileController::class, 'updateLocation']); // update location lat long



});

// Need for app publications
Route::post('app-account-delete', [ProfileController::class, 'appAccountDelete']); //apps delete account inside public folder html: account-delete.html

// Store FCM Token - FM
Route::post('/store-fcm-token', [AuthController::class, 'storeFcmToken']);
Route::post('/delete-fcm-token', [AuthController::class, 'deleteFcmToken']);

//Continue with google and facebook login
// Route::post('/social/login', [SocialLoginController::class, 'SocialLogin']);

// Stripe Webhook
// Route::post('/stripe/webhook', [StripeController::class, 'handleWebhook']);
