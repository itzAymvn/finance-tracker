<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\SalaryMonthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::post('salary-months/period', [SalaryMonthController::class, 'storePeriod'])
    ->name('salary-months.storePeriod');

Route::resource('salary-months', SalaryMonthController::class)
    ->except(['index'])
    ->parameters(['salary-months' => 'salary_month']);

Route::resource('payouts', PayoutController::class);

Route::get('payouts/{payout}/attachment', [AttachmentController::class, 'download'])
    ->name('payouts.attachment');
