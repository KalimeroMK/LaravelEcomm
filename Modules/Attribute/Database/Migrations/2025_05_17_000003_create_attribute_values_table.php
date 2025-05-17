<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attribute_values', function (Blueprint $table) {
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
            $table->unique(['product_id', 'attribute_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
