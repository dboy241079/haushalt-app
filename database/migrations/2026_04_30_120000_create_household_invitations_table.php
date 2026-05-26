<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('household_invitations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('household_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('email')->index();
            $table->string('role', 20)->default('member');
            $table->string('token', 64)->unique();
            $table->string('status', 20)->default('pending')->index();

            $table->foreignId('invited_by_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('accepted_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_invitations');
    }
};