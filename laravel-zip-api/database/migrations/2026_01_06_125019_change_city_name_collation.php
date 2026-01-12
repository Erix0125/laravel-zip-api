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
        // Only apply collation for MySQL/MariaDB databases
        if (env('DB_CONNECTION') !== 'sqlite') {
            Schema::table('cities', function (Blueprint $table) {
                $table->string('name')->collation('utf8mb4_hungarian_ci')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (env('DB_CONNECTION') !== 'sqlite') {
            Schema::table('cities', function (Blueprint $table) {
                $table->string('name')->collation('utf8mb4_unicode_ci')->change();
            });
        }
    }
};
