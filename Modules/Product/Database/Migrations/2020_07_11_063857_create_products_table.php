<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->index();
            $table->string('slug')->unique();
            $table->text('summary')->index();
            $table->longText('description')->nullable()->index();
            $table->integer('stock')->default(1);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->float('price');
            $table->float('discount')->nullable();
            $table->boolean('is_featured')->default(false)->nullable()->index();
            $table->boolean('d_deal')->default(false)->index();
            $table->unsignedBigInteger('brand_id')->nullable()->index();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
