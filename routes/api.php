<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CropController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\ChatController;

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

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    
    // Public crop browsing
    Route::get('crops', [CropController::class, 'index']);
    Route::get('crops/{crop}', [CropController::class, 'show']);
    Route::get('crops/category/{category}', [CropController::class, 'byCategory']);
    Route::get('categories', [CropController::class, 'categories']);
});

// Protected routes (require authentication)
Route::middleware('auth:api')->prefix('v1')->group(function () {
    // User management
    Route::get('auth/profile', [AuthController::class, 'profile']);
    Route::put('auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    
    // Crops management (farmers only)
    Route::middleware('role:farmer')->group(function () {
        Route::get('my-crops', [CropController::class, 'myCrops']);
        Route::post('crops', [CropController::class, 'store']);
        Route::put('crops/{crop}', [CropController::class, 'update']);
        Route::delete('crops/{crop}', [CropController::class, 'destroy']);
    });
    
    // Orders management
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus']);
    
    // Verification
    Route::get('verification', [VerificationController::class, 'show']);
    Route::post('verification/id', [VerificationController::class, 'storeId']);
    Route::post('verification/phone/send', [VerificationController::class, 'sendPhone']);
    Route::post('verification/phone/verify', [VerificationController::class, 'verifyPhone']);
    Route::post('verification/address', [VerificationController::class, 'storeAddress']);
    
    // Chat/Messaging
    Route::get('chats', [ChatController::class, 'index']);
    Route::post('chats', [ChatController::class, 'store']);
    Route::get('chats/{chat}', [ChatController::class, 'show']);
    Route::post('chats/{chat}/messages', [ChatController::class, 'sendMessage']);
    
    // Notifications
    Route::get('notifications', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $user->notifications()->latest()->limit(50)->get(),
                'unread_count' => $user->unreadNotifications()->count(),
            ]
        ]);
    });
    
    // Statistics
    Route::get('stats', function (Request $request) {
        $user = $request->user();
        
        if ($user->isFarmer()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_crops' => $user->crops()->count(),
                    'active_crops' => $user->crops()->where('is_available', true)->count(),
                    'total_orders' => $user->orders()->count(),
                    'pending_orders' => $user->orders()->where('status', 'pending')->count(),
                    'completed_orders' => $user->orders()->where('status', 'completed')->count(),
                    'total_revenue' => $user->orders()->where('status', 'completed')->sum('total_amount'),
                ]
            ]);
        } else {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_orders' => $user->orders()->count(),
                    'pending_orders' => $user->orders()->where('status', 'pending')->count(),
                    'completed_orders' => $user->orders()->where('status', 'completed')->count(),
                    'total_spent' => $user->orders()->where('status', 'completed')->sum('total_amount'),
                ]
            ]);
        }
    });
});
