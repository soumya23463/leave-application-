<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@leavedesk.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('Super@1234'),
                'role'     => 'superadmin',
                'status'   => 'active',
            ]
        );
    }
}
