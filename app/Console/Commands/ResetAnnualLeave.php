<?php

namespace App\Console\Commands;

use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Console\Command;

class ResetAnnualLeave extends Command
{
    protected $signature = 'leave:reset-annual {--year= : Target year (defaults to current year)}';
    protected $description = 'Reset annual leave balances for all employees';

    public function handle(): int
    {
        $year = (int) ($this->option('year') ?? now()->year);
        $defaultDays = 24;
        $employees = User::where('role', 'employee')->where('status', 'active')->get();

        $this->info("Resetting leave balances for year {$year}...");

        foreach ($employees as $employee) {
            $previousBalance = LeaveBalance::where([
                'employee_id' => $employee->id,
                'year'        => $year - 1,
            ])->first();

            $carryForward = $previousBalance ? min($previousBalance->remaining_days, 10) : 0;
            $total = $defaultDays + $carryForward;

            $balance = LeaveBalance::updateOrCreate(
                ['employee_id' => $employee->id, 'year' => $year],
                [
                    'total_days'      => $total,
                    'used_days'       => 0,
                    'remaining_days'  => $total,
                    'carried_forward' => $carryForward,
                ]
            );

        }

        $this->info("Annual leave reset completed for {$employees->count()} employees.");

        return Command::SUCCESS;
    }
}
