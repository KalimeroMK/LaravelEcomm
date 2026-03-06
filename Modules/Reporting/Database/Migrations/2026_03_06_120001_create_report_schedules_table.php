<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            
            // Schedule configuration
            $table->string('frequency'); // daily, weekly, monthly, quarterly
            $table->string('day_of_week')->nullable(); // for weekly: monday, tuesday, etc.
            $table->integer('day_of_month')->nullable(); // for monthly: 1-31
            $table->time('time')->default('08:00:00'); // execution time
            $table->string('timezone')->default('UTC');
            
            // Date range configuration
            $table->string('date_range_type')->default('last_7_days'); // last_7_days, last_30_days, last_month, custom
            $table->integer('date_range_days')->nullable(); // for dynamic ranges
            
            // Recipients
            $table->json('recipients'); // array of email addresses
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['is_active', 'next_run_at']);
            $table->index('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_schedules');
    }
};
