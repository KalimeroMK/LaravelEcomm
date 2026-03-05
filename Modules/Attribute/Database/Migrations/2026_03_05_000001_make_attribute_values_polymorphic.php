<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make attribute_values polymorphic to support multiple models
        Schema::table('attribute_values', function (Blueprint $table): void {
            // Add polymorphic columns
            $table->unsignedBigInteger('attributable_id')->nullable()->after('id');
            $table->string('attributable_type')->nullable()->after('attributable_id');

            // Drop old foreign key and column (keep data migration in mind)
            // For now, we keep product_id for backward compatibility

            // Add index for polymorphic queries
            $table->index(['attributable_id', 'attributable_type']);
        });

        // Migrate existing data: set attributable from product_id
        DB::table('attribute_values')->update([
            'attributable_id' => DB::raw('product_id'),
            'attributable_type' => 'Modules\\Product\\Models\\Product',
        ]);
    }

    public function down(): void
    {
        Schema::table('attribute_values', function (Blueprint $table): void {
            $table->dropIndex(['attributable_id', 'attributable_type']);
            $table->dropColumn(['attributable_id', 'attributable_type']);
        });
    }
};
