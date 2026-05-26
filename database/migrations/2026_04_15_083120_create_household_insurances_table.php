<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('household_insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('provider')->nullable();
            $table->string('policy_number')->nullable();
            $table->string('insurance_type');
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->string('payment_interval');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_insurances');
    }
};