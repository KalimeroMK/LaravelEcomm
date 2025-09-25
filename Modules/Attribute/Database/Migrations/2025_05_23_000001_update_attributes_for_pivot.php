<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove attribute_group_id from attributes table
        Schema::table('attributes', function (Blueprint $table): void {
            $table->dropForeign(['attribute_group_id']);
            $table->dropColumn('attribute_group_id');
        });

        // Create pivot table for attribute <-> attribute_group
        Schema::create('attribute_attribute_group', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('attribute_group_id');
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
            $table->foreign('attribute_group_id')->references('id')->on('attribute_groups')->onDelete('cascade');
            $table->unique(['attribute_id', 'attribute_group_id']);
        });
    }

    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table): void {
            $table->unsignedBigInteger('attribute_group_id')->nullable();
            $table->foreign('attribute_group_id')->references('id')->on('attribute_groups')->onDelete('set null');
        });
        Schema::dropIfExists('attribute_attribute_group');
    }
};
