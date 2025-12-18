<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractImportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Solutions
    Route::resource('solutions', SolutionController::class);
    
    // Customers
    Route::resource('customers', CustomerController::class);
    
    // Services
    Route::resource('services', ServiceController::class);
    
    // Contracts
    Route::resource('contracts', ContractController::class);
    Route::get('/contracts/{contract}/export', [ContractController::class, 'export'])->name('contracts.export');
    Route::post('/contracts/{contract}/attachments', [ContractController::class, 'uploadAttachment'])->name('contracts.attachments.upload');
    Route::get('/contracts/{contract}/attachments/{attachment}/view', [ContractController::class, 'viewAttachment'])->name('contracts.attachments.view');
    
    // Import
    Route::get('/contracts/import', [ContractImportController::class, 'show'])->name('contracts.import.show');
    Route::post('/contracts/import', [ContractImportController::class, 'import'])->name('contracts.import');
    Route::get('/contracts/import/template', [ContractImportController::class, 'downloadTemplate'])->name('contracts.import.template');
    
    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    
    // Reports
    Route::get('/reports/expiry', [ReportController::class, 'expiry'])->name('reports.expiry');
    Route::get('/reports/expiry/export', [ReportController::class, 'export'])->name('reports.expiry.export');

    // Users (Admin)
    Route::resource('users', UserController::class)->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
