<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('trigger_term');
            $table->string('alternative');
            $table->string('goal')->default('allgemein');
            $table->text('reason')->nullable();
            $table->integer('priority')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['trigger_term', 'goal', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_suggestions');
    }
};