<?php

declare(strict_types=1);

namespace Modules\Reporting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;
use Modules\User\Models\User;

/**
 * @property int $id
 * @property int $report_id
 * @property int|null $schedule_id
 * @property string $status
 * @property string $trigger_type
 * @property int|null $triggered_by
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $completed_at
 * @property int|null $execution_time_ms
 * @property array|null $parameters
 * @property int|null $record_count
 * @property float|null $total_amount
 * @property string|null $file_path
 * @property string|null $file_url
 * @property string|null $error_message
 * @property string|null $stack_trace
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Report $report
 * @property-read ReportSchedule|null $schedule
 * @property-read User|null $triggeredBy
 */
class ReportExecution extends Core
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    public const TRIGGER_MANUAL = 'manual';
    public const TRIGGER_SCHEDULED = 'scheduled';
    public const TRIGGER_API = 'api';

    protected $fillable = [
        'report_id',
        'schedule_id',
        'status',
        'trigger_type',
        'triggered_by',
        'started_at',
        'completed_at',
        'execution_time_ms',
        'parameters',
        'record_count',
        'total_amount',
        'file_path',
        'file_url',
        'error_message',
        'stack_trace',
    ];

    protected $casts = [
        'parameters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'execution_time_ms' => 'integer',
        'record_count' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ReportSchedule::class);
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function markAsRunning(): void
    {
        $this->update([
            'status' => self::STATUS_RUNNING,
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(array $data = []): void
    {
        $startedAt = $this->started_at ?? now();
        $completedAt = now();
        
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => $completedAt,
            'execution_time_ms' => $completedAt->diffInMilliseconds($startedAt),
            ...$data,
        ]);
    }

    public function markAsFailed(string $message, ?string $stackTrace = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'completed_at' => now(),
            'error_message' => $message,
            'stack_trace' => $stackTrace,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getDurationFormatted(): string
    {
        if (! $this->execution_time_ms) {
            return '-';
        }

        $seconds = floor($this->execution_time_ms / 1000);
        $milliseconds = $this->execution_time_ms % 1000;

        if ($seconds < 60) {
            return $seconds . '.' . str_pad((string) $milliseconds, 3, '0', STR_PAD_LEFT) . 's';
        }

        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        return $minutes . 'm ' . $seconds . 's';
    }
}
