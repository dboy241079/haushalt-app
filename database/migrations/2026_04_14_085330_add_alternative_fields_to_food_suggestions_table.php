<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_suggestions', function (Blueprint $table) {
            $table->string('alternative_label')->nullable()->after('alternative');
            $table->string('alternative_search_term')->nullable()->after('alternative_label');
            $table->string('alternative_barcode', 64)->nullable()->after('alternative_search_term');
        });
    }

    public function down(): void
    {
        Schema::table('food_suggestions', function (Blueprint $table) {
            $table->dropColumn([
                'alternative_label',
                'alternative_search_term',
                'alternative_barcode',
            ]);
        });
    }
};