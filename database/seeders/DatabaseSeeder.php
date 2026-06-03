<?php

namespace Database\Seeders;

use App\Models\Holiday;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Models\WeekendSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Spatie Roles
        $adminRole    = Role::firstOrCreate(['name' => 'admin',    'guard_name' => 'web']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Weekend Setting
        WeekendSetting::firstOrCreate(['effective_date' => '2024-01-01'], [
            'days'           => ['Saturday', 'Sunday'],
            'effective_date' => '2024-01-01',
            'status'         => true,
        ]);

        // Holidays
        $year = now()->year;
        $holidays = [
            ['name' => 'Republic Day',     'date' => "$year-01-26"],
            ['name' => 'Independence Day', 'date' => "$year-08-15"],
            ['name' => 'Gandhi Jayanti',   'date' => "$year-10-02"],
            ['name' => 'Christmas',        'date' => "$year-12-25"],
            ['name' => 'New Year',         'date' => ($year + 1) . '-01-01'],
        ];
        foreach ($holidays as $h) {
            Holiday::firstOrCreate(['name' => $h['name'], 'date' => $h['date']], array_merge($h, ['status' => true]));
        }

        // Admin user
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);
        $admin->syncRoles([$adminRole]);

        // Demo employee
        $employee = User::firstOrCreate(['email' => 'employee@example.com'], [
            'name'         => 'John Doe',
            'email'        => 'employee@example.com',
            'password'     => Hash::make('password'),
            'role'         => 'employee',
            'phone'        => '9876543210',
            'joining_date' => '2023-01-01',
            'status'       => 'active',
        ]);
        $employee->syncRoles([$employeeRole]);

        // Leave balance for demo employee
        LeaveBalance::firstOrCreate(
            ['employee_id' => $employee->id, 'year' => $year],
            [
                'total_days'      => 24,
                'used_days'       => 0,
                'remaining_days'  => 24,
                'carried_forward' => 0,
            ]
        );
    }
}
