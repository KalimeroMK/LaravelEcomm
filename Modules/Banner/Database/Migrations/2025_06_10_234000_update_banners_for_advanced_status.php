<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table): void {
            $table->date('active_from')->nullable()->after('status');
            $table->date('active_to')->nullable()->after('active_from');
            $table->unsignedBigInteger('max_clicks')->nullable()->after('active_to');
            $table->unsignedBigInteger('max_impressions')->nullable()->after('max_clicks');
            $table->unsignedBigInteger('current_clicks')->default(0)->after('max_impressions');
            $table->unsignedBigInteger('current_impressions')->default(0)->after('current_clicks');
        });

        Schema::create('banner_category', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('banner_id')->constrained('banners')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['banner_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table): void {
            $table->dropColumn([
                'active_from',
                'active_to',
                'max_clicks',
                'max_impressions',
                'current_clicks',
                'current_impressions',
            ]);
        });
        Schema::dropIfExists('banner_category');
    }
};
