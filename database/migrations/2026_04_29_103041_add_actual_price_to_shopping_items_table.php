<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shopping_items', function (Blueprint $table) {
            if (!Schema::hasColumn('shopping_items', 'actual_price')) {
                $table->decimal('actual_price', 10, 2)->nullable()->after('is_bought');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shopping_items', function (Blueprint $table) {
            if (Schema::hasColumn('shopping_items', 'actual_price')) {
                $table->dropColumn('actual_price');
            }
        });
    }
};