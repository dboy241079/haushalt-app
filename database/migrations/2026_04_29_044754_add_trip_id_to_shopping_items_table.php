<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shopping_items', function (Blueprint $table) {
            if (!Schema::hasColumn('shopping_items', 'trip_id')) {
                $table->foreignId('trip_id')
                    ->nullable()
                    ->after('household_id')
                    ->constrained('trips')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('shopping_items', function (Blueprint $table) {
            if (Schema::hasColumn('shopping_items', 'trip_id')) {
                $table->dropConstrainedForeignId('trip_id');
            }
        });
    }
};