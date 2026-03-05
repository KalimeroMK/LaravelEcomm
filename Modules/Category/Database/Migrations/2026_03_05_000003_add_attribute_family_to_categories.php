<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->unsignedBigInteger('attribute_family_id')->nullable();
        });

        // Add foreign key only for non-SQLite databases
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('categories', function (Blueprint $table): void {
                $table->foreign('attribute_family_id')
                    ->references('id')
                    ->on('attribute_families')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('categories', function (Blueprint $table): void {
                $table->dropForeign(['attribute_family_id']);
            });
        }

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('attribute_family_id');
        });
    }
};
