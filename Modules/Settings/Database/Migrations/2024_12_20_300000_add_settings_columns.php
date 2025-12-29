<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            // Payment settings
            $table->json('payment_settings')->nullable()->after('google_map_api_key');

            // Shipping settings
            $table->json('shipping_settings')->nullable()->after('payment_settings');

            // Email settings
            $table->json('email_settings')->nullable()->after('shipping_settings');

            // SEO settings
            $table->json('seo_settings')->nullable()->after('email_settings');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->dropColumn(['payment_settings', 'shipping_settings', 'email_settings', 'seo_settings']);
        });
    }
};
