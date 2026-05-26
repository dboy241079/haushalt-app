<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('list_type', ['packing', 'preparation']);
            $table->string('category')->nullable();
            $table->string('title');
            $table->string('quantity')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_checked')->default(false);
            $table->boolean('is_suggested')->default(false);
            $table->timestamp('checked_at')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_items');
    }
};