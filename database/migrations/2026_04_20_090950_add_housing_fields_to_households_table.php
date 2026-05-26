<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->string('living_mode', 30)->nullable()->after('name'); // rent | ownership
            $table->string('ownership_kind', 30)->nullable()->after('living_mode'); // apartment | house
            $table->string('house_usage', 30)->nullable()->after('ownership_kind'); // self | partial_rent | full_rent
            $table->timestamp('costs_setup_completed_at')->nullable()->after('house_usage');
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn([
                'living_mode',
                'ownership_kind',
                'house_usage',
                'costs_setup_completed_at',
            ]);
        });
    }
};