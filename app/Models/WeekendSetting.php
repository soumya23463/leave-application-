<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeekendSetting extends Model
{
    protected $fillable = [
        'days',
        'effective_date',
        'status',
    ];

    protected $casts = [
        'days' => 'array',
        'effective_date' => 'date',
        'status' => 'boolean',
    ];

    public static function activeWeekendDays(): array
    {
        $setting = static::where('status', true)
            ->where('effective_date', '<=', now()->toDateString())
            ->latest('effective_date')
            ->first();

        return $setting?->days ?? ['Saturday', 'Sunday'];
    }
}
