<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SAPController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SaFindingController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SauditController;
use App\Http\Controllers\Audit5SExportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Auth
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/auth/login', [AuthController::class, 'postLogin']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('request/access', [AuthController::class, 'requestAccess']);

// Form Akses Publik
Route::get('/form', [FormController::class, 'index'])->name('form');
Route::get('/form/{name}', [FormController::class, 'indexShop'])->name('form.shop');
Route::get('/qr/{name}', [FormController::class, 'qrScan'])->name('form.scan');

// Audit 5S Public Input
Route::post('/5s-form', [SauditController::class, 'store'])->name('saudit.store');
Route::get('/form/audit/5s', [SauditController::class, 'create'])->name('form.5s');
Route::get('/form/audit/5s/{name}', [SauditController::class, 'createShop'])->name('form.5s.shop');

// Risk Assessment Public Submission
Route::post('/risk-assessment', [FormController::class, 'store'])->name('risk-assessment.store');

/*
|--------------------------------------------------------------------------
| Protected Routes (Authentication Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Ganti Password
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('changePassword');

    /*
    |--------------------------------------------------------------------------
    | 5S Audit Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/5s', [SauditController::class, 'index'])->name('saudit.index');
    Route::get('/5s-form', [SauditController::class, 'create'])->name('saudit.create');
    Route::get('/5s-form/{id}/edit', [SauditController::class, 'edit'])->name('saudit.edit');
    Route::put('/5s-form/{id}', [SauditController::class, 'update'])->name('saudit.update');
    Route::delete('/5s-form/{id}', [SauditController::class, 'destroy'])->name('saudit.destroy');
    Route::get('/5s/{id}', [SauditController::class, 'show'])->name('saudit.show');
    Route::get('/5s-form/{id}/view', [SauditController::class, 'show'])->name('saudit.view');
    Route::get('/saudit/dashboard', [SauditController::class, 'dashboard'])->name('saudit.dashboard');
    Route::get('/export-audit-5s/{id}', [Audit5SExportController::class, 'export'])->name('audit5s.export');

    /*
    |--------------------------------------------------------------------------
    | Shop Dropdown Management
    |--------------------------------------------------------------------------
    */
    Route::resource('shop', ShopController::class)->names([
        'index' => 'shop.index',
        'create' => 'shop.create',
        'store' => 'shop.store',
        'show' => 'shop.show',
        'edit' => 'shop.edit',
        'update' => 'shop.update',
        'destroy' => 'shop.destroy',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Dropdown General Management
    |--------------------------------------------------------------------------
    */
    Route::get('/dropdown', [DropdownController::class, 'index']);
    Route::post('/dropdown/store', [DropdownController::class, 'store']);
    Route::patch('/dropdown/update/{id}', [DropdownController::class, 'update']);
    Route::delete('/dropdown/delete/{id}', [DropdownController::class, 'delete']);

    /*
    |--------------------------------------------------------------------------
    | Rules Management
    |--------------------------------------------------------------------------
    */
    Route::get('/rule', [RulesController::class, 'index']);
    Route::post('/rule/store', [RulesController::class, 'store']);
    Route::patch('/rule/update/{id}', [RulesController::class, 'update']);
    Route::delete('/rule/delete/{id}', [RulesController::class, 'delete']);

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */
    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user/store', [UserController::class, 'store']);
    Route::post('/user/store-partner', [UserController::class, 'storePartner']);
    Route::patch('/user/update/{user}', [UserController::class, 'update']);
    Route::get('/user/revoke/{user}', [UserController::class, 'revoke']);
    Route::get('/user/access/{user}', [UserController::class, 'access']);
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    /*
    |--------------------------------------------------------------------------
    | Risk Assessment Action & Follow-up
    |--------------------------------------------------------------------------
    */
    Route::get('/formAction/{assessmentId}', [SaFindingController::class, 'index'])->name('formAction');
    Route::post('/formAction/store', [SaFindingController::class, 'store'])->name('formAction.store');
    Route::get('/form-action/view/{id}', [SaFindingController::class, 'view'])->name('formAction.view');
    Route::get('/risk/followup/{id}', [SaFindingController::class, 'followup'])->name('risk.followup');

    /*
    |--------------------------------------------------------------------------
    | Export Files
    |--------------------------------------------------------------------------
    */
    Route::get('/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [ExportController::class, 'exportPDF'])->name('export.pdf');
    Route::get('/download/{filename}', [FormController::class, 'download'])->name('download');

    /*
    |--------------------------------------------------------------------------
    | Misc
    |--------------------------------------------------------------------------
    */
    Route::delete('/assessments/{id}', [HomeController::class, 'destroy'])->name('assessments.destroy');
});
