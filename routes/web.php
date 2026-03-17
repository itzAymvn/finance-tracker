<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryMonthController;
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

Route::resource('payouts', PayoutController::class)
    ->middleware(['auth']);

Route::get('payouts/{payout}/attachment', [AttachmentController::class, 'download'])
    ->middleware(['auth'])
    ->name('payouts.attachment');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
