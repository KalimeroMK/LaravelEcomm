<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Address type
            $table->enum('type', ['shipping', 'billing', 'both'])->default('shipping');
            $table->boolean('is_default')->default(false);
            
            // Contact info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            
            // Address fields
            $table->string('country');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('post_code', 20);
            
            // Optional: for delivery instructions
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
