<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 3)->unique(); // ISO 4217
            $table->string('name');
            $table->string('symbol', 8);
            $table->string('symbol_position', 5)->default('left'); // left|right
            // Multiplier from the base currency (base has 1.0).
            $table->decimal('exchange_rate', 12, 6)->default(1);
            $table->unsignedTinyInteger('decimals')->default(2);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
