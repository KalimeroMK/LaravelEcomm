<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_carts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->json('cart_data'); // Store cart items as JSON
            $table->decimal('total_amount', 10, 2);
            $table->integer('total_items');
            $table->timestamp('last_activity')->index();
            $table->timestamp('first_email_sent')->nullable();
            $table->timestamp('second_email_sent')->nullable();
            $table->timestamp('third_email_sent')->nullable();
            $table->boolean('converted')->default(false);
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['last_activity', 'converted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
