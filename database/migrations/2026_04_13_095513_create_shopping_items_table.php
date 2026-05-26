<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopping_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('quantity')->nullable();
            $table->string('category')->default('Allgemein');
            $table->text('note')->nullable();
            $table->foreignId('added_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('bought_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_bought')->default(false);
            $table->timestamp('bought_at')->nullable();
            $table->date('needed_for_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_items');
    }
};