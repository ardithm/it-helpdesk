<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Guest Routes — hanya bisa diakses bila belum login
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ─────────────────────────────────────────────────────────────────────────────
// Authenticated Routes
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── User Dashboard ────────────────────────────────────────────────────────
    Route::middleware('role:user,helpdesk,technician,admin')->group(function () {
        Route::get('/dashboard/user', fn () => view('dashboard.user'))->name('user.dashboard');
    });

    // ── Helpdesk Dashboard ────────────────────────────────────────────────────
    Route::middleware('role:helpdesk,admin')->group(function () {
        Route::get('/dashboard/helpdesk', fn () => view('dashboard.helpdesk'))->name('helpdesk.dashboard');
    });

    // ── Technician Dashboard ──────────────────────────────────────────────────
    Route::middleware('role:technician,admin')->group(function () {
        Route::get('/dashboard/technician', fn () => view('dashboard.technician'))->name('technician.dashboard');
    });

    // ── Admin Dashboard ───────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard/admin', fn () => view('dashboard.admin'))->name('admin.dashboard');
    });

});

// ─────────────────────────────────────────────────────────────────────────────
// Root redirect
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->role . '.dashboard')
        : redirect()->route('login');
});
