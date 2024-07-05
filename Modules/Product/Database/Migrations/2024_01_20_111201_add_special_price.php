<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->float('special_price')->nullable();
            $table->date('special_price_start')->nullable();
            $table->date('special_price_end')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->float('special_price')->nullable();
            $table->date('special_price_start')->nullable();
            $table->date('special_price_end')->nullable();
        });
    }
};
