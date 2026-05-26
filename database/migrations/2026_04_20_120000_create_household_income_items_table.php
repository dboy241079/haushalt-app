<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('household_income_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category')->default('Vermietung & Einnahmen');
            $table->enum('interval', ['monthly', 'quarterly', 'half_yearly', 'yearly', 'one_time'])->default('monthly');
            $table->decimal('amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_auto_generated')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_income_items');
    }
};