<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('household_quick_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('quick_type', 100);
            $table->string('room', 100)->nullable();
            $table->string('note', 255)->nullable();
            $table->date('done_on');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_quick_entries');
    }
};