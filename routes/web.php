<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Controller Imports
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// Inventory
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Inventory\LabelController;

// Procurement
use App\Http\Controllers\Procurement\PurchaseOrderController;

// Finance
use App\Http\Controllers\Finance\ReportController;
use App\Http\Controllers\Finance\FinancialReportController;
use App\Http\Controllers\Finance\FinancialController;

// Admin
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TeamController;

// Authentication & Verification
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Corrected Root URL to render the Premium Welcome Page
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ====================================================
// AUTHENTICATED ROUTES (User Must Be Logged In)
// ====================================================
Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. INVENTORY
    Route::post('/items/{item}/transaction', [ItemController::class, 'transaction'])->name('items.transaction');
    Route::post('/items/{item}/adjust', [ItemController::class, 'transaction'])->name('items.adjust');
    Route::get('/items/{item}/history', [ItemController::class, 'history'])->name('items.history');
    Route::get('/items/{item}/label', [LabelController::class, 'show'])->name('items.label');
    Route::resource('items', ItemController::class);

    // 3. PROCUREMENT
    Route::get('/purchase-orders/check-pending', [PurchaseOrderController::class, 'checkPending'])->name('purchase-orders.check');
    Route::post('/purchase-orders/{id}/receive-item', [PurchaseOrderController::class, 'receiveItem'])->name('purchase-orders.receive-item');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'markReceived'])->name('purchase-orders.receive');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    // 4. SUPPLIERS
    Route::resource('suppliers', SupplierController::class);

    // 5. REPORTS
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/financial', [FinancialReportController::class, 'index'])->name('reports.financial');

    // 5.5 FINANCE
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index')->middleware('role:admin,super_admin');
    Route::get('/financial/report', [FinancialController::class, 'report'])->name('financial.report')->middleware('role:admin,super_admin');

    // 6. ADMIN
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::resource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::resource('team', TeamController::class)->only(['index', 'store', 'destroy'])->middleware('role:admin,lab_manager,super_admin');

    // 7. PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/user/profile', function () { return redirect()->route('profile.edit'); });
    Route::put('/user/profile-information', [ProfileController::class, 'update'])->name('user-profile-information.update');
    Route::delete('/user/other-browser-sessions', [ProfileController::class, 'destroy'])->name('other-browser-sessions.destroy');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Security: Confirm Password
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Security: Update Password
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    // 8. LOGOUT
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


// ====================================================
// EMAIL VERIFICATION ROUTES
// ====================================================
Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});


// ====================================================
// GUEST ROUTES
// ====================================================
Route::middleware('guest')->group(function () {

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    });
