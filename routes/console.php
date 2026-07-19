<?php

use App\Console\Commands\ResetAnnualLeave;
use App\Console\Commands\SendBirthdayWishes;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Annual leave reset — runs every year on Jan 1st at midnight
Schedule::command(ResetAnnualLeave::class)->yearlyOn(1, 1, '00:00');

// Birthday wishes — runs every day at 9 AM
Schedule::command(SendBirthdayWishes::class)->dailyAt('09:00');
