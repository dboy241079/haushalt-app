<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('destination_name')->nullable();
            $table->string('destination_address')->nullable();

            $table->unsignedSmallInteger('persons')->default(2);

            $table->enum('travel_mode', ['camper', 'car', 'other'])->default('camper');
            $table->enum('status', ['planned', 'preparing', 'ready', 'done'])->default('planned');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};