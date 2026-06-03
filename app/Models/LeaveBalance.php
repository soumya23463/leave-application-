<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $fillable = [
        'employee_id',
        'total_days',
        'used_days',
        'remaining_days',
        'carried_forward',
        'year',
    ];

    protected $casts = [
        'total_days' => 'float',
        'used_days' => 'float',
        'remaining_days' => 'float',
        'carried_forward' => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }


}
