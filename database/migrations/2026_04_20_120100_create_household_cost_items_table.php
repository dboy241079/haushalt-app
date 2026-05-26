<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('household_cost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category')->default('Wohnen & Fixkosten');
            $table->string('interval', 30)->default('monthly'); // monthly, quarterly, half_yearly, yearly, one_time
            $table->decimal('amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_auto_generated')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['household_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_cost_items');
    }
};