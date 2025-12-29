<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zone_methods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->onDelete('cascade');
            $table->foreignId('shipping_id')->constrained('shipping')->onDelete('cascade');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('free_shipping_threshold', 10, 2)->nullable(); // Free shipping if order exceeds this amount
            $table->integer('estimated_days')->nullable(); // Estimated delivery days
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zone_methods');
    }
};
