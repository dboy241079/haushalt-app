<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('household_insurances', function (Blueprint $table) {
            $table->foreignId('reminder_event_id')
                ->nullable()
                ->after('created_by_user_id')
                ->constrained('household_events')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('household_insurances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reminder_event_id');
        });
    }
};