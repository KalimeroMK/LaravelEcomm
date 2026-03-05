<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create index table for fast filtering
        Schema::create('attribute_filter_index', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('attribute_code');
            $table->string('value_text')->nullable();
            $table->integer('value_integer')->nullable();
            $table->decimal('value_decimal', 12, 2)->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->timestamps();

            // Indexes for fast filtering
            $table->index(['attribute_id', 'value_text']);
            $table->index(['attribute_code', 'value_text']);
            $table->index(['product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_filter_index');
    }
};
