<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key and column from products table
        Schema::table('products', function (Blueprint $table): void {
            if (Schema::hasColumn('products', 'condition_id')) {
                $table->dropForeign(['condition_id']);
                $table->dropColumn('condition_id');
            }
        });

        // Drop the conditions table
        Schema::dropIfExists('conditions');
    }

    public function down(): void
    {
        // Recreate the conditions table
        Schema::create('conditions', function (Blueprint $table): void {
            $table->id();
            $table->string('status')->index();
            $table->timestamps();
        });

        // Re-add the condition_id column to products
        Schema::table('products', function (Blueprint $table): void {
            $table->unsignedBigInteger('condition_id')->nullable()->after('description');
            $table->foreign('condition_id')->references('id')->on('conditions')->onDelete('cascade');
        });
    }
};
