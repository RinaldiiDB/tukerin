<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEmployeeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// User (Nasabah) Routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/qr', [UserController::class, 'qr'])->name('qr');
    Route::get('/transactions', [UserController::class, 'transactions'])->name('transactions');
    Route::get('/rewards', [UserController::class, 'rewards'])->name('rewards');
    Route::get('/rewards/create', [UserController::class, 'createReward'])->name('rewards.create');
    Route::post('/rewards', [UserController::class, 'storeReward'])->name('rewards.store');
});

// Employee (Pegawai) Routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::get('/scan', [EmployeeController::class, 'scan'])->name('scan');
    Route::get('/scan/user/{qr_code}', [EmployeeController::class, 'lookupUser'])->name('scan.user');
    Route::get('/scan/bottle/{barcode}', [EmployeeController::class, 'lookupBottle'])->name('scan.bottle');
    Route::post('/transactions', [EmployeeController::class, 'storeTransaction'])->name('transactions.store');
    Route::get('/transactions', [EmployeeController::class, 'transactions'])->name('transactions');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // CRUD Employees
    Route::resource('employees', AdminEmployeeController::class);
    
    // Directory & Logs
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
    Route::get('/redemptions', [AdminController::class, 'redemptions'])->name('redemptions');
    
    // Redemption Approvals
    Route::post('/redemptions/{id}/approve', [AdminController::class, 'approveRedemption'])->name('redemptions.approve');
    Route::post('/redemptions/{id}/reject', [AdminController::class, 'rejectRedemption'])->name('redemptions.reject');
});
