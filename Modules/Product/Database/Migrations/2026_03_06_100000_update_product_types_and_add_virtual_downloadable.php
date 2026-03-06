<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update product type enum to include downloadable and virtual
        Schema::table('products', function (Blueprint $table): void {
            // For MySQL/MariaDB we need to modify the enum
            // Note: This might need adjustment based on your database driver
            $table->enum('type', ['simple', 'configurable', 'variant', 'downloadable', 'virtual'])
                ->default('simple')
                ->change();
            
            // Virtual product fields
            $table->boolean('is_virtual')->default(false)->after('type')
                ->comment('No shipping required');
            $table->boolean('is_downloadable')->default(false)->after('is_virtual')
                ->comment('Has downloadable files');
            
            // Service fields for virtual products
            $table->timestamp('service_starts_at')->nullable()->after('special_price_end');
            $table->timestamp('service_ends_at')->nullable()->after('service_starts_at');
            $table->integer('service_duration_minutes')->nullable()->after('service_ends_at')
                ->comment('Service duration in minutes');
            
            // Download limit settings
            $table->integer('max_downloads')->nullable()->after('service_duration_minutes')
                ->comment('Max number of downloads per purchase');
            $table->integer('download_expiry_days')->nullable()->after('max_downloads')
                ->comment('Days until download link expires');
        });

        // Create product downloads table
        Schema::create('product_downloads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['product_id', 'is_active']);
        });

        // Create order downloads table for tracking customer downloads
        Schema::create('order_downloads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_download_id')->constrained('product_downloads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('downloads_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['order_id', 'user_id']);
            $table->unique(['order_id', 'product_download_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_downloads');
        Schema::dropIfExists('product_downloads');

        Schema::table('products', function (Blueprint $table): void {
            // Revert enum (this might vary by database)
            $table->enum('type', ['simple', 'configurable', 'variant'])
                ->default('simple')
                ->change();
            
            $table->dropColumn([
                'is_virtual',
                'is_downloadable',
                'service_starts_at',
                'service_ends_at',
                'service_duration_minutes',
                'max_downloads',
                'download_expiry_days',
            ]);
        });
    }
};
