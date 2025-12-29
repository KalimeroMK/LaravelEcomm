<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('tracking_number')->nullable()->after('transaction_reference');
            $table->string('tracking_carrier')->nullable()->after('tracking_number');
            $table->timestamp('shipped_at')->nullable()->after('tracking_carrier');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['tracking_number', 'tracking_carrier', 'shipped_at']);
        });
    }
};
