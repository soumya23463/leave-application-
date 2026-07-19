<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveBalanceSeeder extends Seeder
{
    /**
     * Create a current-year leave balance for every existing employee
     * (skips anyone who already has one for this year).
     */
    public function run(): void
    {
        $year = now()->year;
        $totalDays = 24;

        $employees = User::where('role', 'employee')->get();

        foreach ($employees as $employee) {
             // dummy usage

            LeaveBalance::updateOrCreate(
                ['employee_id' => $employee->id, 'year' => $year],
                [
                    'total_days'      => $totalDays,
                    'used_days'       => 0,
                    'remaining_days'  => $totalDays,
                    'carried_forward' => 0,
                ]
            );
        }

        $this->command->info("Leave balances seeded for {$employees->count()} employees (year {$year}).");
    }
}
