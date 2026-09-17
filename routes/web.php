<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\HelpdeskDashboardController;
use App\Http\Controllers\Dashboard\TechnicianDashboardController;
use App\Http\Controllers\Dashboard\UserDashboardController;
use App\Http\Controllers\Ticket\HelpdeskTicketController;
use App\Http\Controllers\Ticket\TechnicianTicketController;
use App\Http\Controllers\Ticket\TicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\Admin\SlaPolicyController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Root — redirect berdasarkan login status
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (! auth()->check()) return redirect()->route('login');
    return match (auth()->user()->role) {
        'admin'      => redirect()->route('admin.dashboard'),
        'helpdesk'   => redirect()->route('helpdesk.dashboard'),
        'technician' => redirect()->route('technician.dashboard'),
        default      => redirect()->route('user.dashboard'),
    };
});

// ─────────────────────────────────────────────────────────────────────────────
// Guest Routes
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ─────────────────────────────────────────────────────────────────────────────
// Authenticated Routes
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Profile (semua role) ──────────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',               [ProfileController::class, 'show'])->name('show');
        Route::put('/',               [ProfileController::class, 'update'])->name('update');
        Route::put('/password',       [ProfileController::class, 'updatePassword'])->name('password');
    });

    // ── Knowledge Base (semua role) ───────────────────────────────────────────
    Route::prefix('knowledge-base')->name('kb.')->group(function () {
        Route::get('/',               [KnowledgeBaseController::class, 'index'])->name('index');
        Route::get('/create',         [KnowledgeBaseController::class, 'create'])->name('create');
        Route::post('/',              [KnowledgeBaseController::class, 'store'])->name('store');
        Route::get('/{knowledgeBase}',       [KnowledgeBaseController::class, 'show'])->name('show');
        Route::get('/{knowledgeBase}/edit',  [KnowledgeBaseController::class, 'edit'])->name('edit');
        Route::put('/{knowledgeBase}',       [KnowledgeBaseController::class, 'update'])->name('update');
        Route::delete('/{knowledgeBase}',    [KnowledgeBaseController::class, 'destroy'])->name('destroy');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // USER ROUTES
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:user,helpdesk,technician,admin')->group(function () {
        Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('user.dashboard');

        Route::prefix('my-tickets')->name('user.tickets.')->group(function () {
            Route::get('/',                             [TicketController::class, 'index'])->name('index');
            Route::get('/create',                       [TicketController::class, 'create'])->name('create');
            Route::post('/',                            [TicketController::class, 'store'])->name('store');
            Route::get('/{ticket}',                     [TicketController::class, 'show'])->name('show');
            Route::post('/{ticket}/comment',            [TicketController::class, 'addComment'])->name('comment');
            Route::post('/{ticket}/confirm',            [TicketController::class, 'confirmResolution'])->name('confirm');
            Route::get('/{ticket}/rate',                [TicketController::class, 'showRateForm'])->name('rate');
            Route::post('/{ticket}/rate',               [TicketController::class, 'submitRating'])->name('rate.submit');
        });
    });

    // ─────────────────────────────────────────────────────────────────────────
    // HELPDESK ROUTES
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:helpdesk,admin')->group(function () {
        Route::get('/dashboard/helpdesk', [HelpdeskDashboardController::class, 'index'])->name('helpdesk.dashboard');

        Route::prefix('helpdesk/tickets')->name('helpdesk.tickets.')->group(function () {
            Route::get('/',                         [HelpdeskTicketController::class, 'index'])->name('index');
            Route::get('/{ticket}',                 [HelpdeskTicketController::class, 'show'])->name('show');
            Route::post('/{ticket}/assign',         [HelpdeskTicketController::class, 'assign'])->name('assign');
            Route::patch('/{ticket}/classify',      [HelpdeskTicketController::class, 'classify'])->name('classify');
            Route::patch('/{ticket}/status',        [HelpdeskTicketController::class, 'updateStatus'])->name('status');
            Route::post('/{ticket}/comment',        [HelpdeskTicketController::class, 'addComment'])->name('comment');
        });
    });

    // ─────────────────────────────────────────────────────────────────────────
    // TECHNICIAN ROUTES
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:technician,admin')->group(function () {
        Route::get('/dashboard/technician', [TechnicianDashboardController::class, 'index'])->name('technician.dashboard');

        Route::prefix('technician/tickets')->name('technician.tickets.')->group(function () {
            Route::get('/',                         [TechnicianTicketController::class, 'index'])->name('index');
            Route::get('/{ticket}',                 [TechnicianTicketController::class, 'show'])->name('show');
            Route::post('/{ticket}/start',          [TechnicianTicketController::class, 'startWork'])->name('start');
            Route::post('/{ticket}/waiting',        [TechnicianTicketController::class, 'setWaiting'])->name('waiting');
            Route::post('/{ticket}/resolve',        [TechnicianTicketController::class, 'resolve'])->name('resolve');
            Route::post('/{ticket}/comment',        [TechnicianTicketController::class, 'addComment'])->name('comment');
        });
    });

    // ─────────────────────────────────────────────────────────────────────────
    // ADMIN ROUTES
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Manajemen User
        Route::get('/users',                    [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create',             [UserController::class, 'create'])->name('users.create');
        Route::post('/users',                   [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',        [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',             [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle',     [UserController::class, 'toggleActive'])->name('users.toggle');

        // Manajemen Departemen
        Route::get('/departments',              [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments',             [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        // Manajemen Kategori Tiket
        Route::get('/categories',                          [TicketCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories',                         [TicketCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{ticketCategory}',         [TicketCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{ticketCategory}',      [TicketCategoryController::class, 'destroy'])->name('categories.destroy');

        // SLA Policy
        Route::get('/sla',                     [SlaPolicyController::class, 'index'])->name('sla.index');
        Route::post('/sla',                    [SlaPolicyController::class, 'store'])->name('sla.store');
        Route::put('/sla/{slaPolicy}',         [SlaPolicyController::class, 'update'])->name('sla.update');

        // Aset IT
        Route::get('/assets',                  [AssetController::class, 'index'])->name('assets.index');
        Route::get('/assets/create',           [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets',                 [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}',          [AssetController::class, 'show'])->name('assets.show');
        Route::get('/assets/{asset}/edit',     [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{asset}',          [AssetController::class, 'update'])->name('assets.update');

        // Laporan
        Route::get('/reports',                 [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
    });

});
