<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('household_insurances', function (Blueprint $table) {
            $table->unsignedInteger('cancellation_notice_days')->nullable()->after('ends_at');
        });

        Schema::table('household_insurance_documents', function (Blueprint $table) {
            $table->string('document_type')->nullable()->after('household_insurance_id');
        });
    }

    public function down(): void
    {
        Schema::table('household_insurance_documents', function (Blueprint $table) {
            $table->dropColumn('document_type');
        });

        Schema::table('household_insurances', function (Blueprint $table) {
            $table->dropColumn('cancellation_notice_days');
        });
    }
};