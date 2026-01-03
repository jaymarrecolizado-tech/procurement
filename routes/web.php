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
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApprovalDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SupplierRepositoryController;

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
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/approvals', [ApprovalDashboardController::class, 'index'])->name('approvals.dashboard');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    
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
        Route::get('/{purchaseRequest}/assign-approvers', [PurchaseRequestController::class, 'assignApprovers'])->name('assign-approvers');
        Route::post('/{purchaseRequest}/assign-approvers', [PurchaseRequestController::class, 'storeApprovers'])->name('store-approvers');
        Route::post('/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])->name('approve');
        Route::post('/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject'])->name('reject');
    });
    
    Route::prefix('rfqs')->name('rfqs.')->group(function () {
        Route::get('/', [RFQController::class, 'index'])->name('index');
        Route::get('/create/{purchaseRequest}', [RFQController::class, 'create'])->name('create');
        Route::post('/create/{purchaseRequest}', [RFQController::class, 'store'])->name('store');
        Route::get('/{rfq}/edit', [RFQController::class, 'edit'])->name('edit');
        Route::put('/{rfq}', [RFQController::class, 'update'])->name('update');
        Route::patch('/{rfq}/status', [RFQController::class, 'updateStatus'])->name('update-status');
        Route::get('/{rfq}/assign-approvers', [RFQController::class, 'assignApprovers'])->name('assign-approvers');
        Route::post('/{rfq}/assign-approvers', [RFQController::class, 'storeApprovers'])->name('store-approvers');
        Route::post('/{rfq}/approve', [RFQController::class, 'approve'])->name('approve');
        Route::post('/{rfq}/reject', [RFQController::class, 'reject'])->name('reject');
        Route::get('/{rfq}', [RFQController::class, 'show'])->name('show');
    });
    
    Route::prefix('bac-documents')->name('bac-documents.')->group(function () {
        Route::get('/', [BacDocumentController::class, 'index'])->name('index');
        Route::get('/pending-approvals', [BacDocumentController::class, 'pendingApprovals'])->name('pending-approvals');
        Route::get('/create/{purchaseRequest}', [BacDocumentController::class, 'create'])->name('create');
        Route::post('/create/{purchaseRequest}', [BacDocumentController::class, 'store'])->name('store');
        Route::get('/{bacDocument}', [BacDocumentController::class, 'show'])->name('show');
        Route::get('/{bacDocument}/edit', [BacDocumentController::class, 'edit'])->name('edit');
        Route::put('/{bacDocument}', [BacDocumentController::class, 'update'])->name('update');
        Route::delete('/{bacDocument}', [BacDocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{bacDocument}/assign-approvers', [BacDocumentController::class, 'assignApprovers'])->name('assign-approvers');
        Route::post('/{bacDocument}/assign-approvers', [BacDocumentController::class, 'storeApprovers'])->name('store-approvers');
        Route::post('/{bacDocument}/approve', [BacDocumentController::class, 'approve'])->name('approve');
        Route::post('/{bacDocument}/reject', [BacDocumentController::class, 'reject'])->name('reject');
    });
    
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create/{purchaseRequest}', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/create/{purchaseRequest}', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
        Route::get('/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
        Route::put('/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update');
        Route::patch('/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('update-status');
        Route::get('/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'generatePDF'])->name('pdf');
        Route::get('/{purchaseOrder}/preview', [PurchaseOrderController::class, 'previewPDF'])->name('preview');
    });

    // Document Management Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::post('/pr/{purchaseRequest}', [DocumentController::class, 'uploadForPR'])->name('upload-pr');
        Route::post('/rfq/{rfq}', [DocumentController::class, 'uploadForRFQ'])->name('upload-rfq');
        Route::post('/po/{purchaseOrder}', [DocumentController::class, 'uploadForPO'])->name('upload-po');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
        Route::get('/{document}/preview', [DocumentController::class, 'preview'])->name('preview');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
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

    // Supplier Repository Routes
    Route::prefix('supplier-repository')->name('supplier-repository.')->group(function () {
        Route::get('/', [SupplierRepositoryController::class, 'index'])->name('index');
        Route::get('/create', [SupplierRepositoryController::class, 'create'])->name('create');
        Route::post('/', [SupplierRepositoryController::class, 'store'])->name('store');
        Route::get('/{quotationHistory}', [SupplierRepositoryController::class, 'show'])->name('show');
        Route::get('/{quotationHistory}/edit', [SupplierRepositoryController::class, 'edit'])->name('edit');
        Route::put('/{quotationHistory}', [SupplierRepositoryController::class, 'update'])->name('update');
        Route::delete('/{quotationHistory}', [SupplierRepositoryController::class, 'destroy'])->name('destroy');
        Route::get('/api/suppliers', [SupplierRepositoryController::class, 'getSuppliers'])->name('api.suppliers');
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
    
    // Admin User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
    });
});
