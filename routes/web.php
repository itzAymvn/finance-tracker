<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryMonthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::post('salary-months/period', [SalaryMonthController::class, 'storePeriod'])
    ->middleware(['auth'])
    ->name('salary-months.storePeriod');

Route::resource('salary-months', SalaryMonthController::class)
    ->except(['index'])
    ->parameters(['salary-months' => 'salary_month'])
    ->middleware(['auth']);

Route::resource('transactions', TransactionController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('backup')->middleware(['auth'])->group(function () {
    Route::get('/', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/export', [BackupController::class, 'export'])->name('backup.export');
    Route::get('/download/{name}', [BackupController::class, 'download'])->name('backup.download');
    Route::post('/restore', [BackupController::class, 'restore'])->name('backup.restore');
});

require __DIR__.'/auth.php';
