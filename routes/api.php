<?php

use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\MessageController as ApiMessageController;
use App\Http\Controllers\Api\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\ServiceController as ApiServiceController;
use App\Http\Controllers\Api\ServiceOfferController as ApiServiceOfferController;
use Illuminate\Support\Facades\Route;

// Backward compatibility routes (without v1 prefix)
Route::get('login', function () {
    return response()->json([
        'success' => true,
        'message' => 'Login endpoint information',
        'endpoint' => '/api/login',
        'method' => 'POST',
        'required_fields' => [
            'email' => 'string (required)',
            'password' => 'string (required)'
        ],
        'example' => [
            'email' => 'user@example.com',
            'password' => 'password123'
        ],
        'response' => [
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'token' => 'api_token_here',
                'user' => 'user_object'
            ]
        ]
    ]);
})->name('api.login.info');

Route::post('login', [ApiAuthController::class, 'login'])->name('api.login');

Route::get('register', function () {
    return response()->json([
        'success' => true,
        'message' => 'Register endpoint information',
        'endpoint' => '/api/register',
        'method' => 'POST',
        'required_fields' => [
            'name' => 'string (required)',
            'email' => 'string (required, unique)',
            'password' => 'string (required, min:8)',
            'password_confirmation' => 'string (required)'
        ],
        'optional_fields' => [
            'phone' => 'string (optional, unique)',
            'user_type' => 'customer|provider (optional, default: customer)'
        ],
        'example' => [
            'name' => 'User Name',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0123456789',
            'user_type' => 'customer'
        ],
        'response' => [
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'data' => [
                'token' => 'api_token_here',
                'user' => 'user_object'
            ]
        ]
    ]);
})->name('api.register.info');

Route::post('register', [ApiAuthController::class, 'register'])->name('api.register');

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/status', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is up',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Public endpoints
    Route::get('categories', [ApiCategoryController::class, 'index']);
    Route::get('categories/{slug}/details', [ApiCategoryController::class, 'show']);
    Route::get('categories/{category}/subcategories', [ApiCategoryController::class, 'subcategories'])
        ->whereNumber('category');

    Route::get('services', [ApiServiceController::class, 'index']);
    Route::get('services/{service}', [ApiServiceController::class, 'show'])->whereNumber('service');

    // Auth
    Route::get('auth/login', function () {
        return response()->json([
            'success' => true,
            'message' => 'Login endpoint information',
            'endpoint' => '/api/v1/auth/login',
            'method' => 'POST',
            'required_fields' => [
                'email' => 'string (required)',
                'password' => 'string (required)'
            ],
            'example' => [
                'email' => 'user@example.com',
                'password' => 'password123'
            ],
            'response' => [
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'token' => 'api_token_here',
                    'user' => 'user_object'
                ]
            ]
        ]);
    })->name('api.v1.auth.login.info');

    Route::post('auth/login', [ApiAuthController::class, 'login']);

    Route::get('auth/register', function () {
        return response()->json([
            'success' => true,
            'message' => 'Register endpoint information',
            'endpoint' => '/api/v1/auth/register',
            'method' => 'POST',
            'required_fields' => [
                'name' => 'string (required)',
                'email' => 'string (required, unique)',
                'password' => 'string (required, min:8)',
                'password_confirmation' => 'string (required)'
            ],
            'optional_fields' => [
                'phone' => 'string (optional, unique)',
                'user_type' => 'customer|provider (optional, default: customer)'
            ],
            'example' => [
                'name' => 'User Name',
                'email' => 'user@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'phone' => '0123456789',
                'user_type' => 'customer'
            ],
            'response' => [
                'success' => true,
                'message' => 'تم إنشاء الحساب بنجاح',
                'data' => [
                    'token' => 'api_token_here',
                    'user' => 'user_object'
                ]
            ]
        ]);
    })->name('api.v1.auth.register.info');

    Route::post('auth/register', [ApiAuthController::class, 'register']);

    Route::middleware('api.token')->group(function () {
        Route::post('auth/logout', [ApiAuthController::class, 'logout']);
        Route::get('auth/profile', [ApiAuthController::class, 'profile']);
        Route::put('auth/profile', [ApiAuthController::class, 'updateProfile']);

        // Services
        Route::get('services/me', [ApiServiceController::class, 'myServices']);
        Route::post('services', [ApiServiceController::class, 'store']);
        Route::put('services/{service}', [ApiServiceController::class, 'update'])->whereNumber('service');
        Route::delete('services/{service}', [ApiServiceController::class, 'destroy'])->whereNumber('service');

        // Service offers
        Route::get('offers', [ApiServiceOfferController::class, 'index']);
        Route::post('services/{service}/offers', [ApiServiceOfferController::class, 'store'])->whereNumber('service');
        Route::post('offers/{offer}/accept', [ApiServiceOfferController::class, 'accept'])->whereNumber('offer');
        Route::post('offers/{offer}/reject', [ApiServiceOfferController::class, 'reject'])->whereNumber('offer');
        Route::post('offers/{offer}/deliver', [ApiServiceOfferController::class, 'deliver'])->whereNumber('offer');
        Route::post('offers/{offer}/review', [ApiServiceOfferController::class, 'review'])->whereNumber('offer');

        // Notifications
        Route::get('notifications', [ApiNotificationController::class, 'index']);
        Route::post('notifications/mark-all-read', [ApiNotificationController::class, 'markAllAsRead']);
        Route::post('notifications/{notification}/read', [ApiNotificationController::class, 'markAsRead'])->whereNumber('notification');
        Route::delete('notifications/{notification}', [ApiNotificationController::class, 'destroy'])->whereNumber('notification');

        // Messages
        Route::get('messages', [ApiMessageController::class, 'index']);
        Route::get('messages/{user}', [ApiMessageController::class, 'show'])->whereNumber('user');
        Route::post('messages', [ApiMessageController::class, 'store']);
        Route::delete('messages/{message}', [ApiMessageController::class, 'destroy'])->whereNumber('message');
    });
});
