<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_products_cache', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 64)->unique();
            $table->string('product_name')->nullable();
            $table->string('brand')->nullable();
            $table->string('image_url')->nullable();
            $table->string('nutrition_grade')->nullable();
            $table->json('categories')->nullable();
            $table->json('nutriments')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_products_cache');
    }
};