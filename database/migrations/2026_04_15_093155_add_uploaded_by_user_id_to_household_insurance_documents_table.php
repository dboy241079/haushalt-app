<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('household_insurance_documents', function (Blueprint $table) {
            $table->foreignId('uploaded_by_user_id')
                ->nullable()
                ->after('document_type')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('household_insurance_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('uploaded_by_user_id');
        });
    }
};