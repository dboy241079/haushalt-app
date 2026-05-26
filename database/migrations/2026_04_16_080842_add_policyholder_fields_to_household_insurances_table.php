<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('household_insurances', function (Blueprint $table) {
            $table->string('provider_email')->nullable()->after('provider_city');

            $table->string('insured_first_name')->nullable()->after('provider_email');
            $table->string('insured_last_name')->nullable()->after('insured_first_name');
            $table->string('insured_street')->nullable()->after('insured_last_name');
            $table->string('insured_zip')->nullable()->after('insured_street');
            $table->string('insured_city')->nullable()->after('insured_zip');
            $table->string('insured_email')->nullable()->after('insured_city');
            $table->string('insured_phone')->nullable()->after('insured_email');
        });
    }

    public function down(): void
    {
        Schema::table('household_insurances', function (Blueprint $table) {
            $table->dropColumn([
                'provider_email',
                'insured_first_name',
                'insured_last_name',
                'insured_street',
                'insured_zip',
                'insured_city',
                'insured_email',
                'insured_phone',
            ]);
        });
    }
};