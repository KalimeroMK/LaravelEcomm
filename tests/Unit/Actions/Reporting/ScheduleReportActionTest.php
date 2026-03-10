<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Reporting;

use Modules\Reporting\Actions\CreateReportAction;
use Modules\Reporting\Actions\ScheduleReportAction;
use Modules\Reporting\DTOs\ReportDTO;
use Modules\Reporting\DTOs\ReportScheduleDTO;
use Modules\Reporting\Models\Report;
use Modules\Reporting\Models\ReportSchedule;
use Tests\Unit\Actions\ActionTestCase;

class ScheduleReportActionTest extends ActionTestCase
{
    private function createTestReport(): Report
    {
        $dto = new ReportDTO(
            name: 'Test Report',
            slug: 'test-report',
            description: null,
            type: Report::TYPE_SALES,
            format: Report::FORMAT_HTML,
            filters: null,
            columns: null,
            grouping: null,
            sorting: null,
            createdBy: 1,
            isTemplate: false,
            isPublic: false,
            sortOrder: 0,
        );

        return app(CreateReportAction::class)->execute($dto);
    }

    public function testExecuteCreatesDailySchedule(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_DAILY,
            dayOfWeek: null,
            dayOfMonth: null,
            time: '09:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_7_DAYS,
            dateRangeDays: null,
            recipients: ['admin@example.com'],
            subject: 'Daily Report',
            message: 'Here is your daily report',
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(ReportSchedule::class, $result);
        $this->assertEquals($report->id, $result->report_id);
        $this->assertEquals(ReportSchedule::FREQUENCY_DAILY, $result->frequency);
        $this->assertEquals('09:00:00', $result->time);
        $this->assertNotNull($result->next_run_at);
    }

    public function testExecuteCreatesWeeklySchedule(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_WEEKLY,
            dayOfWeek: 'monday',
            dayOfMonth: null,
            time: '08:00:00',
            timezone: 'America/New_York',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_30_DAYS,
            dateRangeDays: null,
            recipients: ['team@example.com', 'manager@example.com'],
            subject: 'Weekly Report',
            message: null,
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(ReportSchedule::FREQUENCY_WEEKLY, $result->frequency);
        $this->assertEquals('monday', $result->day_of_week);
        $this->assertEquals('America/New_York', $result->timezone);
    }

    public function testExecuteCreatesMonthlySchedule(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_MONTHLY,
            dayOfWeek: null,
            dayOfMonth: 1,
            time: '00:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_MONTH,
            dateRangeDays: null,
            recipients: ['finance@example.com'],
            subject: 'Monthly Report',
            message: 'Monthly financial report',
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(ReportSchedule::FREQUENCY_MONTHLY, $result->frequency);
        $this->assertEquals(1, $result->day_of_month);
    }

    public function testExecuteCreatesInactiveSchedule(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_DAILY,
            dayOfWeek: null,
            dayOfMonth: null,
            time: '09:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_7_DAYS,
            dateRangeDays: null,
            recipients: [],
            subject: null,
            message: null,
            isActive: false,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $result = $action->execute($dto);

        $this->assertFalse($result->is_active);
    }

    public function testExecuteWithCustomDateRange(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_DAILY,
            dayOfWeek: null,
            dayOfMonth: null,
            time: '09:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_CUSTOM,
            dateRangeDays: 14,
            recipients: ['user@example.com'],
            subject: 'Custom Report',
            message: null,
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(ReportSchedule::DATE_RANGE_CUSTOM, $result->date_range_type);
        $this->assertEquals(14, $result->date_range_days);
    }

    public function testUpdateModifiesSchedule(): void
    {
        $report = $this->createTestReport();

        // Create initial schedule
        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_DAILY,
            dayOfWeek: null,
            dayOfMonth: null,
            time: '09:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_7_DAYS,
            dateRangeDays: null,
            recipients: ['old@example.com'],
            subject: 'Old Subject',
            message: null,
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $schedule = $action->execute($dto);

        // Update the schedule
        $updateDto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_WEEKLY,
            dayOfWeek: 'friday',
            dayOfMonth: null,
            time: '17:00:00',
            timezone: 'Europe/London',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_30_DAYS,
            dateRangeDays: null,
            recipients: ['new@example.com', 'extra@example.com'],
            subject: 'New Subject',
            message: 'New message',
            isActive: false,
            createdBy: 1,
        );

        $result = $action->update($schedule, $updateDto);

        $this->assertEquals(ReportSchedule::FREQUENCY_WEEKLY, $result->frequency);
        $this->assertEquals('friday', $result->day_of_week);
        $this->assertEquals('17:00:00', $result->time);
        $this->assertEquals('Europe/London', $result->timezone);
        $this->assertEquals(['new@example.com', 'extra@example.com'], $result->recipients);
        $this->assertEquals('New Subject', $result->subject);
        $this->assertEquals('New message', $result->message);
        $this->assertFalse($result->is_active);
    }

    public function testExecuteCalculatesNextRunForDaily(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_DAILY,
            dayOfWeek: null,
            dayOfMonth: null,
            time: '09:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_7_DAYS,
            dateRangeDays: null,
            recipients: [],
            subject: null,
            message: null,
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->next_run_at);
        // Next run should be tomorrow at 09:00 UTC
        $expectedTime = now()->addDay()->setTime(9, 0);
        $this->assertEquals($expectedTime->format('Y-m-d H'), $result->next_run_at->format('Y-m-d H'));
    }

    public function testExecuteCalculatesNextRunForWeekly(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_WEEKLY,
            dayOfWeek: 'monday',
            dayOfMonth: null,
            time: '08:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_30_DAYS,
            dateRangeDays: null,
            recipients: [],
            subject: null,
            message: null,
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->next_run_at);
        $this->assertEquals('Monday', $result->next_run_at->format('l'));
    }

    public function testExecuteCreatesQuarterlySchedule(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_QUARTERLY,
            dayOfWeek: null,
            dayOfMonth: 1,
            time: '00:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_QUARTER,
            dateRangeDays: null,
            recipients: ['quarterly@example.com'],
            subject: 'Quarterly Report',
            message: null,
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(ReportSchedule::FREQUENCY_QUARTERLY, $result->frequency);
        $this->assertEquals(ReportSchedule::DATE_RANGE_LAST_QUARTER, $result->date_range_type);
    }

    public function testExecuteCreatesBiweeklySchedule(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_BIWEEKLY,
            dayOfWeek: null,
            dayOfMonth: null,
            time: '10:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_7_DAYS,
            dateRangeDays: null,
            recipients: ['biweekly@example.com'],
            subject: 'Biweekly Report',
            message: null,
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(ReportSchedule::FREQUENCY_BIWEEKLY, $result->frequency);
    }

    public function testUpdateRecalculatesNextRun(): void
    {
        $report = $this->createTestReport();

        // Create daily schedule
        $dto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_DAILY,
            dayOfWeek: null,
            dayOfMonth: null,
            time: '09:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_7_DAYS,
            dateRangeDays: null,
            recipients: [],
            subject: null,
            message: null,
            isActive: true,
            createdBy: 1,
        );

        $action = app(ScheduleReportAction::class);
        $schedule = $action->execute($dto);
        $originalNextRun = $schedule->next_run_at->copy();

        // Update to monthly
        $updateDto = new ReportScheduleDTO(
            reportId: $report->id,
            frequency: ReportSchedule::FREQUENCY_MONTHLY,
            dayOfWeek: null,
            dayOfMonth: 15,
            time: '12:00:00',
            timezone: 'UTC',
            dateRangeType: ReportSchedule::DATE_RANGE_LAST_MONTH,
            dateRangeDays: null,
            recipients: [],
            subject: null,
            message: null,
            isActive: true,
            createdBy: 1,
        );

        $result = $action->update($schedule, $updateDto);

        // Next run should be recalculated
        $this->assertNotEquals($originalNextRun, $result->next_run_at);
    }
}
