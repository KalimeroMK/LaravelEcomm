<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // sales, products, customers, inventory, etc.
            $table->string('format')->default('html'); // html, pdf, excel, csv
            
            // Report configuration
            $table->json('filters')->nullable(); // date_range, categories, status, etc.
            $table->json('columns')->nullable(); // which columns to include
            $table->json('grouping')->nullable(); // group by fields
            $table->json('sorting')->nullable(); // sort by fields
            
            // Report metadata
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_template')->default(false);
            $table->boolean('is_public')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['type', 'is_template']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
