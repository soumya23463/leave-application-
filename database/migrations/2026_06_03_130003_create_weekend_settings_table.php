<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekend_settings', function (Blueprint $table) {
            $table->id();
            $table->json('days'); // array of day names e.g. ["Saturday","Sunday"]
            $table->date('effective_date');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekend_settings');
    }
};
