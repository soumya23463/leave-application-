<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\WeekendSetting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LeaveCalculator
{
    public static function calculate(string $fromDate, string $toDate): float
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);

        $weekendDays = WeekendSetting::activeWeekendDays();

        $holidays = Holiday::where('status', true)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $count = 0;
        $period = CarbonPeriod::create($from, $to);

        foreach ($period as $date) {
            if (in_array($date->dayName, $weekendDays)) {
                continue;
            }
            if (in_array($date->toDateString(), $holidays)) {
                continue;
            }
            $count++;
        }

        return (float) $count;
    }
}
