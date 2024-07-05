<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->index();
            $table->string('code')->unique()->index();
            $table->string('type')->default(\Modules\Attribute\Models\Attribute::TYPE_STRING);
            $table->string('display')->default(\Modules\Attribute\Models\Attribute::DISPLAY_SELECT);
            $table->boolean('filterable')->default(0);
            $table->boolean('configurable')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
