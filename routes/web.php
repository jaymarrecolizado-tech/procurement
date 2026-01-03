<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\RFQController;
use App\Http\Controllers\BacDocumentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\CanvassController;
use App\Http\Controllers\SupplierQuotationController;

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
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // Rate limit: 5 attempts per minute
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1'); // Rate limit: 3 registrations per minute
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.edit');
    Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/password', [AuthController::class, 'changePassword'])->name('profile.password');
    
    // Purchase Requests routes
    Route::prefix('purchase-requests')->name('purchase-requests.')->group(function () {
        Route::get('/', [PurchaseRequestController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseRequestController::class, 'create'])->name('create');
        Route::post('/', [PurchaseRequestController::class, 'store'])->name('store');
        Route::get('/{purchaseRequest}', [PurchaseRequestController::class, 'show'])->name('show');
        Route::get('/{purchaseRequest}/edit', [PurchaseRequestController::class, 'edit'])->name('edit');
        Route::put('/{purchaseRequest}', [PurchaseRequestController::class, 'update'])->name('update');
        Route::delete('/{purchaseRequest}', [PurchaseRequestController::class, 'destroy'])->name('destroy');
        Route::patch('/{purchaseRequest}/status', [PurchaseRequestController::class, 'updateStatus'])->name('update-status');
        Route::get('/{purchaseRequest}/pdf', [PurchaseRequestController::class, 'generatePDF'])->name('pdf');
        Route::get('/{purchaseRequest}/preview', [PurchaseRequestController::class, 'previewPDF'])->name('preview');
    });
    
    Route::prefix('rfqs')->name('rfqs.')->group(function () {
        Route::get('/', [RFQController::class, 'index'])->name('index');
        Route::get('/create/{purchaseRequest}', [RFQController::class, 'create'])->name('create');
        Route::post('/create/{purchaseRequest}', [RFQController::class, 'store'])->name('store');
        Route::get('/{rfq}/edit', [RFQController::class, 'edit'])->name('edit');
        Route::put('/{rfq}', [RFQController::class, 'update'])->name('update');
        Route::patch('/{rfq}/status', [RFQController::class, 'updateStatus'])->name('update-status');
        Route::get('/{rfq}', [RFQController::class, 'show'])->name('show');
    });
    
    Route::prefix('bac-documents')->name('bac-documents.')->group(function () {
        Route::get('/', [BacDocumentController::class, 'index'])->name('index');
    });
    
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
    });
    
    Route::prefix('canvasses')->name('canvasses.')->group(function () {
        Route::get('/', [CanvassController::class, 'index'])->name('index');
        Route::get('/create/{rfq}', [CanvassController::class, 'create'])->name('create');
        Route::post('/create/{rfq}', [CanvassController::class, 'store'])->name('store');
        Route::get('/{canvass}', [CanvassController::class, 'show'])->name('show');
        Route::get('/{canvass}/edit', [CanvassController::class, 'edit'])->name('edit');
        Route::put('/{canvass}', [CanvassController::class, 'update'])->name('update');
        Route::patch('/{canvass}/status', [CanvassController::class, 'updateStatus'])->name('update-status');
    });
    
    Route::prefix('supplier-quotations')->name('supplier-quotations.')->group(function () {
        Route::get('/create/{canvass}', [SupplierQuotationController::class, 'create'])->name('create');
        Route::post('/create/{canvass}', [SupplierQuotationController::class, 'store'])->name('store');
        Route::get('/{supplierQuotation}', [SupplierQuotationController::class, 'show'])->name('show');
        Route::get('/{supplierQuotation}/edit', [SupplierQuotationController::class, 'edit'])->name('edit');
        Route::put('/{supplierQuotation}', [SupplierQuotationController::class, 'update'])->name('update');
        Route::post('/{supplierQuotation}/select', [SupplierQuotationController::class, 'select'])->name('select');
        Route::delete('/{supplierQuotation}', [SupplierQuotationController::class, 'destroy'])->name('destroy');
    });
});
