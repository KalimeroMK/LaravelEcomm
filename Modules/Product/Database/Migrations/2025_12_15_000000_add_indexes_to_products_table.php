<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            // Individual indexes
            $table->index('status');
            $table->index('price');
            $table->index('created_at');

            // Composite indexes for common query patterns
            $table->index(['status', 'created_at']); // For "Newest Active"
            $table->index(['status', 'price']);      // For "Active Price" filtering
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex(['status']);
            $table->dropIndex(['price']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['status', 'price']);
        });
    }
};
