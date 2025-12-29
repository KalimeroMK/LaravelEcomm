<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_securities', function (Blueprint $table): void {
            $table->json('recovery_codes')->nullable()->after('google2fa_secret');
        });
    }

    public function down(): void
    {
        Schema::table('login_securities', function (Blueprint $table): void {
            $table->dropColumn('recovery_codes');
        });
    }
};
