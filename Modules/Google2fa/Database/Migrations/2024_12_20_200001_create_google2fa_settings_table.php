<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google2fa_settings', function (Blueprint $table): void {
            $table->id();
            $table->boolean('enforce_for_admins')->default(false);
            $table->boolean('enforce_for_users')->default(false);
            $table->json('enforced_roles')->nullable();
            $table->integer('recovery_codes_count')->default(10);
            $table->boolean('require_backup_codes')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google2fa_settings');
    }
};
