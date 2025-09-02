<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_analytics', function (Blueprint $table): void {
            $table->id();
            $table->string('email_type'); // newsletter, abandoned_cart, etc.
            $table->string('email_subject');
            $table->string('recipient_email')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('campaign_id')->nullable()->index();
            $table->timestamp('sent_at')->index();
            $table->timestamp('opened_at')->nullable()->index();
            $table->timestamp('clicked_at')->nullable()->index();
            $table->string('clicked_url')->nullable();
            $table->boolean('bounced')->default(false);
            $table->boolean('unsubscribed')->default(false);
            $table->timestamp('unsubscribed_at')->nullable();
            $table->json('metadata')->nullable(); // Store additional data
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['email_type', 'sent_at']);
            $table->index(['campaign_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_analytics');
    }
};
