<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryMonthController;
use App\Http\Controllers\SubscriptionController;
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

Route::resource('categories', CategoryController::class)
    ->except(['show'])
    ->middleware(['auth']);

Route::middleware('auth')->group(function () {
    Route::post('subscriptions/{subscription}/pause', [SubscriptionController::class, 'pause'])->name('subscriptions.pause');
    Route::post('subscriptions/{subscription}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('backup')->middleware(['auth'])->group(function () {
    Route::get('/', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/export', [BackupController::class, 'export'])->name('backup.export');
    Route::post('/settings', [BackupController::class, 'updateSettings'])->name('backup.settings');
    Route::get('/download/{name}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/{name}', [BackupController::class, 'delete'])->name('backup.delete');
    Route::post('/restore', [BackupController::class, 'restore'])->name('backup.restore');
});

require __DIR__.'/auth.php';
