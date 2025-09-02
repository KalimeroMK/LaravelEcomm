<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_behavior_tracking', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id')->nullable()->index();
            $table->string('event_type'); // page_view, click, scroll, time_on_page, etc.
            $table->string('page_url')->index();
            $table->string('page_title')->nullable();
            $table->string('referrer')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('event_data')->nullable(); // Additional event-specific data
            $table->integer('duration')->nullable(); // Time spent in seconds
            $table->timestamp('event_timestamp')->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['event_type', 'event_timestamp']);
            $table->index(['user_id', 'event_timestamp']);
            $table->index(['session_id', 'event_timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_behavior_tracking');
    }
};
