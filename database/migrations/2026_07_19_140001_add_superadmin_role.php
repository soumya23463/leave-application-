<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin', 'admin', 'employee') NOT NULL DEFAULT 'employee'");
    }

    public function down(): void
    {
        // Demote any superadmins before shrinking the enum, so no row is invalid.
        DB::table('users')->where('role', 'superadmin')->update(['role' => 'admin']);
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee'");
    }
};
