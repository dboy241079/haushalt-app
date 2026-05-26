<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('household_trip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('household_trips')->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('packing'); // packing, prep, documents
            $table->boolean('is_done')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_trip_items');
    }
};