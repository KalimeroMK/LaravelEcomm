<?php

declare(strict_types=1);

namespace Modules\Reporting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Core;
use Modules\User\Models\User;

/**
 * @property int $id
 * @property int $report_id
 * @property string $frequency
 * @property string|null $day_of_week
 * @property int|null $day_of_month
 * @property string $time
 * @property string $timezone
 * @property string $date_range_type
 * @property int|null $date_range_days
 * @property array $recipients
 * @property string|null $subject
 * @property string|null $message
 * @property bool $is_active
 * @property \Carbon\Carbon|null $last_run_at
 * @property \Carbon\Carbon|null $next_run_at
 * @property int $created_by
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Report $report
 * @property-read User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ReportExecution> $executions
 */
class ReportSchedule extends Core
{
    use HasFactory;

    public const FREQUENCY_DAILY = 'daily';
    public const FREQUENCY_WEEKLY = 'weekly';
    public const FREQUENCY_BIWEEKLY = 'biweekly';
    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_QUARTERLY = 'quarterly';

    public const DATE_RANGE_LAST_7_DAYS = 'last_7_days';
    public const DATE_RANGE_LAST_30_DAYS = 'last_30_days';
    public const DATE_RANGE_LAST_MONTH = 'last_month';
    public const DATE_RANGE_LAST_QUARTER = 'last_quarter';
    public const DATE_RANGE_LAST_YEAR = 'last_year';
    public const DATE_RANGE_CUSTOM = 'custom';

    protected $fillable = [
        'report_id',
        'frequency',
        'day_of_week',
        'day_of_month',
        'time',
        'timezone',
        'date_range_type',
        'date_range_days',
        'recipients',
        'subject',
        'message',
        'is_active',
        'last_run_at',
        'next_run_at',
        'created_by',
    ];

    protected $casts = [
        'recipients' => 'array',
        'is_active' => 'boolean',
        'day_of_month' => 'integer',
        'date_range_days' => 'integer',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(ReportExecution::class, 'schedule_id');
    }

    public function calculateNextRun(): ?\Carbon\Carbon
    {
        $now = now($this->timezone);
        $time = explode(':', $this->time);
        
        $nextRun = match ($this->frequency) {
            self::FREQUENCY_DAILY => $now->copy()->addDay()->setTime((int) $time[0], (int) $time[1]),
            
            self::FREQUENCY_WEEKLY => $now->copy()
                ->next($this->day_of_week ?? 'monday')
                ->setTime((int) $time[0], (int) $time[1]),
            
            self::FREQUENCY_BIWEEKLY => $now->copy()
                ->addWeeks(2)
                ->setTime((int) $time[0], (int) $time[1]),
            
            self::FREQUENCY_MONTHLY => $now->copy()
                ->addMonth()
                ->setDay($this->day_of_month ?? 1)
                ->setTime((int) $time[0], (int) $time[1]),
            
            self::FREQUENCY_QUARTERLY => $now->copy()
                ->addMonths(3)
                ->setDay($this->day_of_month ?? 1)
                ->setTime((int) $time[0], (int) $time[1]),
            
            default => null,
        };

        return $nextRun?->setTimezone('UTC');
    }

    public function getDateRange(): array
    {
        $now = now();
        
        return match ($this->date_range_type) {
            self::DATE_RANGE_LAST_7_DAYS => [
                'from' => $now->copy()->subDays(7)->startOfDay(),
                'to' => $now->copy()->endOfDay(),
            ],
            self::DATE_RANGE_LAST_30_DAYS => [
                'from' => $now->copy()->subDays(30)->startOfDay(),
                'to' => $now->copy()->endOfDay(),
            ],
            self::DATE_RANGE_LAST_MONTH => [
                'from' => $now->copy()->subMonth()->startOfMonth(),
                'to' => $now->copy()->subMonth()->endOfMonth(),
            ],
            self::DATE_RANGE_LAST_QUARTER => [
                'from' => $now->copy()->subQuarter()->startOfQuarter(),
                'to' => $now->copy()->subQuarter()->endOfQuarter(),
            ],
            self::DATE_RANGE_LAST_YEAR => [
                'from' => $now->copy()->subYear()->startOfYear(),
                'to' => $now->copy()->subYear()->endOfYear(),
            ],
            default => [
                'from' => $now->copy()->subDays($this->date_range_days ?? 30)->startOfDay(),
                'to' => $now->copy()->endOfDay(),
            ],
        };
    }

    public function markAsRun(): void
    {
        $this->update([
            'last_run_at' => now(),
            'next_run_at' => $this->calculateNextRun(),
        ]);
    }

    public function isDue(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->next_run_at === null) {
            return true;
        }

        return $this->next_run_at->isPast();
    }
}
