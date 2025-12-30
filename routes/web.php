<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.edit');
    Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [AuthController::class, 'changePassword'])->name('profile.password');
    
    // Procurement routes (to be implemented in next phases)
    Route::prefix('purchase-requests')->name('purchase-requests.')->group(function () {
        Route::get('/', function () {
            return 'Purchase Requests - Coming Soon';
        })->name('index');
    });
    
    Route::prefix('rfqs')->name('rfqs.')->group(function () {
        Route::get('/', function () {
            return 'RFQ & Canvass - Coming Soon';
        })->name('index');
    });
    
    Route::prefix('bac-documents')->name('bac-documents.')->group(function () {
        Route::get('/', function () {
            return 'BAC Documents - Coming Soon';
        })->name('index');
    });
    
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', function () {
            return 'Purchase Orders - Coming Soon';
        })->name('index');
    });
    
    Route::prefix('canvasses')->name('canvasses.')->group(function () {
        Route::get('/', function () {
            return 'My Canvass Tasks - Coming Soon';
        })->name('index');
    });
});
