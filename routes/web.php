<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerificationController;

// Home page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->name('verification.send');
});

// Logout (authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard route (authenticated users)
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        return app(AdminController::class)->dashboard();
    } elseif ($user->isFarmer()) {
        return app(FarmerController::class)->dashboard();
    } elseif ($user->isBuyer()) {
        return app(BuyerController::class)->dashboard();
    }
    
    abort(403);
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes (authenticated users)
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
    Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('update-avatar');
});

// Farmer routes
Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [FarmerController::class, 'analytics'])->name('analytics');
    Route::get('/crops', [FarmerController::class, 'crops'])->name('crops.index');
    Route::get('/crops/create', [FarmerController::class, 'createCrop'])->name('crops.create');
    Route::post('/crops', [FarmerController::class, 'storeCrop'])->name('crops.store');
    Route::get('/crops/{crop}', [FarmerController::class, 'showCrop'])->name('crops.show');
    Route::get('/crops/{crop}/edit', [FarmerController::class, 'editCrop'])->name('crops.edit');
    Route::put('/crops/{crop}', [FarmerController::class, 'updateCrop'])->name('crops.update');
    Route::delete('/crops/{crop}', [FarmerController::class, 'deleteCrop'])->name('crops.delete');
    Route::post('/crops/{crop}/toggle-availability', [FarmerController::class, 'toggleCropAvailability'])->name('crops.toggle-availability');
    Route::get('/orders', [FarmerController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [FarmerController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{order}/confirm', [FarmerController::class, 'confirmOrder'])->name('orders.confirm');
    Route::post('/orders/{order}/deliver', [FarmerController::class, 'deliverOrder'])->name('orders.deliver');
    Route::get('/bank-details', [FarmerController::class, 'bankDetails'])->name('bank-details');
    Route::post('/bank-details', [FarmerController::class, 'storeBankDetails'])->name('bank-details.store');
    Route::get('/bank-details/edit', [FarmerController::class, 'editBankDetails'])->name('bank-details.edit');
    Route::put('/bank-details', [FarmerController::class, 'updateBankDetails'])->name('bank-details.update');
    Route::get('/payment-receipts', [FarmerController::class, 'paymentReceipts'])->name('payment-receipts');
    Route::post('/payment-receipts/{receipt}/verify', [FarmerController::class, 'verifyPaymentReceipt'])->name('payment-receipts.verify');
    Route::post('/payment-receipts/{receipt}/reject', [FarmerController::class, 'rejectPaymentReceipt'])->name('payment-receipts.reject');
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');
    Route::post('/chats', [ChatController::class, 'create'])->name('chats.create');
    Route::post('/chats/{chat}/message', [ChatController::class, 'sendMessage'])->name('chats.send-message');
    Route::delete('/chats/{chat}', [ChatController::class, 'delete'])->name('chats.delete');
    Route::get('/chats/start/crop/{crop}', [ChatController::class, 'startFromCrop'])->name('chats.start.crop');
    Route::get('/chats/start/order/{order}', [ChatController::class, 'startFromOrder'])->name('chats.start.order');
});

// Buyer routes
Route::middleware(['auth', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', [BuyerController::class, 'dashboard'])->name('dashboard');
    Route::get('/crops', [BuyerController::class, 'browseCrops'])->name('crops.browse');
    Route::get('/crops/{crop}', [BuyerController::class, 'showCrop'])->name('crops.show');
    Route::post('/crops/{crop}/cart', [BuyerController::class, 'addToCart'])->name('crops.add-to-cart');
    Route::get('/cart', [BuyerController::class, 'cart'])->name('cart');
    Route::put('/cart', [BuyerController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart', [BuyerController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [BuyerController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [BuyerController::class, 'placeOrder'])->name('orders.place');
    Route::get('/orders', [BuyerController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [BuyerController::class, 'showOrder'])->name('orders.show');
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');
    Route::post('/chats', [ChatController::class, 'create'])->name('chats.create');
    Route::post('/chats/{chat}/message', [ChatController::class, 'sendMessage'])->name('chats.send-message');
    Route::delete('/chats/{chat}', [ChatController::class, 'delete'])->name('chats.delete');
    Route::get('/chats/start/crop/{crop}', [ChatController::class, 'startFromCrop'])->name('chats.start.crop');
    Route::get('/chats/start/order/{order}', [ChatController::class, 'startFromOrder'])->name('chats.start.order');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::get('/crops', [AdminController::class, 'crops'])->name('crops');
    Route::get('/crops/{crop}', [AdminController::class, 'showCrop'])->name('crops.show');
    Route::post('/crops/{crop}/toggle-availability', [AdminController::class, 'toggleCropAvailability'])->name('crops.toggle-availability');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
    Route::get('/activities', [AdminController::class, 'activities'])->name('activities');
});

// Verification routes (authenticated users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/verification', [VerificationController::class, 'index'])->name('verification.index');
    Route::post('/verification/id', [VerificationController::class, 'storeIdVerification'])->name('verification.id.store');
    Route::post('/verification/phone/send', [VerificationController::class, 'sendPhoneVerification'])->name('verification.phone.send');
    Route::post('/verification/phone/verify', [VerificationController::class, 'verifyPhone'])->name('verification.phone.verify');
    Route::post('/verification/address', [VerificationController::class, 'storeAddressVerification'])->name('verification.address.store');
    Route::post('/verification/submit', [VerificationController::class, 'submitForReview'])->name('verification.submit');
    Route::get('/verification/status', [VerificationController::class, 'getStatus'])->name('verification.status');
    
    // Admin verification routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/verifications', [VerificationController::class, 'adminIndex'])->name('verifications.index');
        Route::get('/verifications/{verification}', [VerificationController::class, 'adminShow'])->name('verifications.show');
        Route::post('/verifications/{verification}/approve', [VerificationController::class, 'adminApprove'])->name('verifications.approve');
        Route::post('/verifications/{verification}/reject', [VerificationController::class, 'adminReject'])->name('verifications.reject');
    });
});

// Chatbot routes
Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');
Route::post('/chatbot/feedback/{conversation}', [ChatbotController::class, 'submitFeedback'])->name('chatbot.feedback');
Route::get('/chatbot/analytics', [ChatbotController::class, 'getAnalytics'])->name('chatbot.analytics');

// Public crop browsing (no authentication required)
Route::get('/crops', [BuyerController::class, 'browseCrops'])->name('crops.public');
Route::get('/crops/{crop}', [BuyerController::class, 'showCrop'])->name('crops.public.show');
