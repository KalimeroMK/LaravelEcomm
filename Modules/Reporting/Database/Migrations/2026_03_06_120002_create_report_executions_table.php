<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_executions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->foreignId('schedule_id')->nullable()->constrained('report_schedules')->nullOnDelete();
            
            // Execution details
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->string('trigger_type')->default('manual'); // manual, scheduled, api
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('execution_time_ms')->nullable(); // duration in milliseconds
            
            // Report parameters used
            $table->json('parameters')->nullable(); // filters, date range, etc.
            
            // Results
            $table->integer('record_count')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable(); // for sales reports
            $table->string('file_path')->nullable(); // path to generated file
            $table->string('file_url')->nullable(); // temporary URL
            
            // Error handling
            $table->text('error_message')->nullable();
            $table->text('stack_trace')->nullable();
            
            $table->timestamps();
            
            $table->index(['report_id', 'status']);
            $table->index(['status', 'started_at']);
            $table->index('schedule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_executions');
    }
};
