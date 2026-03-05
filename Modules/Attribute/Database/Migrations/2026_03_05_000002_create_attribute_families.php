<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Attribute Families - group of attributes for specific product types
        Schema::create('attribute_families', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Link attributes to families with groups and position
        Schema::create('attribute_family_attributes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attribute_family_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->integer('position')->default(0);
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->unique(['attribute_family_id', 'attribute_id'], 'afa_family_attr_unique');
        });

        // Link categories to attribute families
        Schema::create('category_attribute_families', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_family_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['category_id', 'attribute_family_id'], 'caf_category_family_unique');
        });

        // Add attribute_family_id to products
        Schema::table('products', function (Blueprint $table): void {
            $table->foreignId('attribute_family_id')->nullable()->after('brand_id')->constrained()->onDelete('set null');
        });

        // Add image support for attribute options (for color swatches)
        Schema::table('attribute_options', function (Blueprint $table): void {
            $table->string('color_hex')->nullable()->after('value'); // For color swatches
            $table->string('image')->nullable()->after('color_hex'); // For image swatches
            $table->boolean('is_default')->default(false)->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropForeign(['attribute_family_id']);
            $table->dropColumn('attribute_family_id');
        });

        Schema::dropIfExists('category_attribute_families');
        Schema::dropIfExists('attribute_family_attributes');
        Schema::dropIfExists('attribute_families');

        Schema::table('attribute_options', function (Blueprint $table): void {
            $table->dropColumn(['color_hex', 'image', 'is_default']);
        });
    }
};
