<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('household_insurances', function (Blueprint $table) {
            $table->string('provider_street')->nullable()->after('provider');
            $table->string('provider_zip')->nullable()->after('provider_street');
            $table->string('provider_city')->nullable()->after('provider_zip');
        });
    }

    public function down(): void
    {
        Schema::table('household_insurances', function (Blueprint $table) {
            $table->dropColumn([
                'provider_street',
                'provider_zip',
                'provider_city',
            ]);
        });
    }
};