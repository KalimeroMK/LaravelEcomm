<?php

declare(strict_types=1);

namespace Modules\Reporting\DTOs;

readonly class ReportScheduleDTO
{
    public function __construct(
        public int $reportId,
        public string $frequency,
        public ?string $dayOfWeek,
        public ?int $dayOfMonth,
        public string $time,
        public string $timezone,
        public string $dateRangeType,
        public ?int $dateRangeDays,
        public array $recipients,
        public ?string $subject,
        public ?string $message,
        public bool $isActive,
        public int $createdBy,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            reportId: $data['report_id'],
            frequency: $data['frequency'],
            dayOfWeek: $data['day_of_week'] ?? null,
            dayOfMonth: $data['day_of_month'] ?? null,
            time: $data['time'] ?? '08:00:00',
            timezone: $data['timezone'] ?? 'UTC',
            dateRangeType: $data['date_range_type'] ?? 'last_30_days',
            dateRangeDays: $data['date_range_days'] ?? null,
            recipients: $data['recipients'] ?? [],
            subject: $data['subject'] ?? null,
            message: $data['message'] ?? null,
            isActive: $data['is_active'] ?? true,
            createdBy: $data['created_by'],
        );
    }

    public function toArray(): array
    {
        return [
            'report_id' => $this->reportId,
            'frequency' => $this->frequency,
            'day_of_week' => $this->dayOfWeek,
            'day_of_month' => $this->dayOfMonth,
            'time' => $this->time,
            'timezone' => $this->timezone,
            'date_range_type' => $this->dateRangeType,
            'date_range_days' => $this->dateRangeDays,
            'recipients' => $this->recipients,
            'subject' => $this->subject,
            'message' => $this->message,
            'is_active' => $this->isActive,
            'created_by' => $this->createdBy,
            'next_run_at' => null, // Will be calculated
        ];
    }
}
