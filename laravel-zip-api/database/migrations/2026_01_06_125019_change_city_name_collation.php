<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // We change the column to use Hungarian collation
            $table->string('name')->collation('utf8mb4_hungarian_ci')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Rollback to the default if needed
            $table->string('name')->collation('utf8mb4_unicode_ci')->change();
        });
    }
};
