<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('countries')->nullable(); // Array of country codes
            $table->json('regions')->nullable(); // Array of regions/states
            $table->json('postal_codes')->nullable(); // Array of postal code ranges
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Higher priority zones are checked first
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zones');
    }
};
