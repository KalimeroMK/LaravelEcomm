<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bundle_product', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('bundle_id');
            $table->foreign('bundle_id')->references('id')->on('bundles')->onDelete('cascade');
            $table->primary(['product_id', 'bundle_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_product');
    }
};
