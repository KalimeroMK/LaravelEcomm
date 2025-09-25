<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration is now obsolete. See product_attribute_options and attribute_options.
        Schema::create('attribute_values', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('attribute_id');
            $table->string('text_value')->nullable();
            $table->boolean('boolean_value')->nullable();
            $table->date('date_value')->nullable();
            $table->integer('integer_value')->nullable();
            $table->float('float_value')->nullable();
            $table->string('string_value')->nullable();
            $table->string('url_value')->nullable();
            $table->string('hex_value')->nullable();
            $table->float('decimal_value')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
