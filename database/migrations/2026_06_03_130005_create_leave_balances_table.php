<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_days', 8, 1)->default(0);
            $table->decimal('used_days', 8, 1)->default(0);
            $table->decimal('remaining_days', 8, 1)->default(0);
            $table->decimal('carried_forward', 8, 1)->default(0);
            $table->year('year');
            $table->timestamps();

            $table->unique(['employee_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
