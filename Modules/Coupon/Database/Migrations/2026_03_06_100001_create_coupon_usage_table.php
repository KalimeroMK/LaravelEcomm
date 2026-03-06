<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_usage', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->nullable()->comment('For guest users');
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->timestamp('used_at');
            $table->timestamps();
            
            // Indexes for quick lookups
            $table->index(['coupon_id', 'user_id']);
            $table->index(['coupon_id', 'session_id']);
            $table->index('used_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usage');
    }
};
