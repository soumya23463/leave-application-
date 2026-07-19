<?php

use App\Http\Controllers\AdminLeaveController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveBalanceController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeekendSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Leave Requests (employees + admins)
    Route::post('leave-requests/{leave_request}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{leave_request}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::resource('leave-requests', LeaveRequestController::class);

    // Leave Balances (view own; admin manages)
    Route::resource('leave-balances', LeaveBalanceController::class);

    // Holidays (all view; admin manages)
    Route::resource('holidays', HolidayController::class);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');

    // Discord OAuth
    Route::get('/auth/discord/redirect', [DiscordController::class, 'redirect'])->name('discord.redirect');
    Route::get('/auth/discord/callback', [DiscordController::class, 'callback'])->name('discord.callback');
    Route::get('/auth/discord/disconnect', [DiscordController::class, 'disconnect'])->name('discord.disconnect');

    // Admin-only areas
    Route::middleware('admin')->group(function () {
        Route::resource('admin-leaves', AdminLeaveController::class)
            ->parameters(['admin-leaves' => 'adminLeave']);
        Route::resource('employees', EmployeeController::class);

        Route::get('holidays-import/sample', [HolidayController::class, 'sample'])->name('holidays.sample');
        Route::post('holidays-import', [HolidayController::class, 'import'])->name('holidays.import');

        Route::resource('weekend-settings', WeekendSettingController::class);
    });
});

require __DIR__.'/auth.php';
