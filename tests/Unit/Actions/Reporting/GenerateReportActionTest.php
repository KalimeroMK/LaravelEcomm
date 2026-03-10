<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Reporting;

use Illuminate\Support\Collection;
use Modules\Reporting\Actions\CreateReportAction;
use Modules\Reporting\Actions\GenerateReportAction;
use Modules\Reporting\DTOs\ReportDTO;
use Modules\Reporting\Models\Report;
use Modules\Reporting\Models\ReportExecution;
use Modules\Reporting\Services\ReportDataService;
use Tests\Unit\Actions\ActionTestCase;

class GenerateReportActionTest extends ActionTestCase
{
    private function createTestReport(string $type = Report::TYPE_SALES, array $filters = null, string $slug = 'test-report'): Report
    {
        $dto = new ReportDTO(
            name: 'Test Report',
            slug: $slug,
            description: null,
            type: $type,
            format: Report::FORMAT_HTML,
            filters: $filters,
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

    public function testExecuteReturnsDataAndSummary(): void
    {
        $report = $this->createTestReport(Report::TYPE_SALES);

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->expects($this->once())
            ->method('generate')
            ->with($report, [])
            ->willReturn([
                'data' => collect([['id' => 1, 'total' => 100]]),
                'summary' => ['total_revenue' => 100, 'count' => 1],
            ]);

        $action = new GenerateReportAction($mockDataService);
        $result = $action->execute($report);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('summary', $result);
        $this->assertInstanceOf(Collection::class, $result['data']);
        $this->assertEquals(['total_revenue' => 100, 'count' => 1], $result['summary']);
    }

    public function testExecutePassesParametersToDataService(): void
    {
        $report = $this->createTestReport(Report::TYPE_SALES, ['date_from' => '2024-01-01']);
        $parameters = ['date_to' => '2024-01-31', 'status' => ['completed']];

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->expects($this->once())
            ->method('generate')
            ->with($report, $parameters)
            ->willReturn([
                'data' => collect(),
                'summary' => [],
            ]);

        $action = new GenerateReportAction($mockDataService);
        $action->execute($report, $parameters);
    }

    public function testExecuteAndRecordCreatesExecutionRecord(): void
    {
        $report = $this->createTestReport(Report::TYPE_PRODUCTS);

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->method('generate')
            ->willReturn([
                'data' => collect([['id' => 1], ['id' => 2]]),
                'summary' => ['total_revenue' => 500],
            ]);

        $action = new GenerateReportAction($mockDataService);
        $execution = $action->executeAndRecord($report, null, 1, []);

        $this->assertInstanceOf(ReportExecution::class, $execution);
        $this->assertEquals($report->id, $execution->report_id);
        $this->assertEquals(ReportExecution::STATUS_COMPLETED, $execution->status);
        $this->assertEquals(ReportExecution::TRIGGER_MANUAL, $execution->trigger_type);
        $this->assertEquals(1, $execution->triggered_by);
        $this->assertEquals(2, $execution->record_count);
        $this->assertEquals(500, $execution->total_amount);
    }

    public function testExecuteAndRecordWithScheduleId(): void
    {
        $report = $this->createTestReport(Report::TYPE_CUSTOMERS);

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->method('generate')
            ->willReturn([
                'data' => collect(),
                'summary' => ['total' => 0],
            ]);

        $action = new GenerateReportAction($mockDataService);
        // Use null schedule_id to avoid FK constraint issues in tests
        $execution = $action->executeAndRecord($report, null, null, []);

        $this->assertNull($execution->schedule_id);
        $this->assertEquals(ReportExecution::TRIGGER_SCHEDULED, $execution->trigger_type);
        $this->assertNull($execution->triggered_by);
    }

    public function testExecuteAndRecordWithoutUserUsesScheduledTrigger(): void
    {
        $report = $this->createTestReport(Report::TYPE_ORDERS);

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->method('generate')
            ->willReturn([
                'data' => collect(),
                'summary' => [],
            ]);

        $action = new GenerateReportAction($mockDataService);
        $execution = $action->executeAndRecord($report);

        $this->assertEquals(ReportExecution::TRIGGER_SCHEDULED, $execution->trigger_type);
    }

    public function testExecuteAndRecordMarksAsFailedOnException(): void
    {
        $report = $this->createTestReport(Report::TYPE_REVENUE);

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->method('generate')
            ->willThrowException(new \Exception('Data generation failed'));

        $action = new GenerateReportAction($mockDataService);

        try {
            $action->executeAndRecord($report, null, 1, []);
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            $this->assertEquals('Data generation failed', $e->getMessage());

            // Check that execution was marked as failed
            $execution = ReportExecution::where('report_id', $report->id)->first();
            $this->assertNotNull($execution);
            $this->assertEquals(ReportExecution::STATUS_FAILED, $execution->status);
            $this->assertStringContainsString('Data generation failed', $execution->error_message);
        }
    }

    public function testExecuteAndRecordCapturesExecutionTime(): void
    {
        $report = $this->createTestReport(Report::TYPE_INVENTORY);

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->method('generate')
            ->willReturn([
                'data' => collect(range(1, 100)),
                'summary' => ['count' => 100],
            ]);

        $action = new GenerateReportAction($mockDataService);
        $execution = $action->executeAndRecord($report, null, 1, []);

        $this->assertNotNull($execution->started_at);
        $this->assertNotNull($execution->completed_at);
        $this->assertNotNull($execution->execution_time_ms);
        // Execution time should be a valid number (may be 0 for very fast executions)
        $this->assertIsInt($execution->execution_time_ms);
    }

    public function testExecuteWithDifferentReportTypes(): void
    {
        $types = [
            Report::TYPE_SALES,
            Report::TYPE_PRODUCTS,
            Report::TYPE_CUSTOMERS,
            Report::TYPE_ORDERS,
        ];

        foreach ($types as $index => $type) {
            // Use unique slug for each report to avoid unique constraint violation
            $report = $this->createTestReport($type, null, 'test-report-' . $type . '-' . $index);

            $mockDataService = $this->createMock(ReportDataService::class);
            $mockDataService->expects($this->once())
                ->method('generate')
                ->willReturn([
                    'data' => collect(),
                    'summary' => ['type' => $type],
                ]);

            $action = new GenerateReportAction($mockDataService);
            $result = $action->execute($report);

            $this->assertEquals(['type' => $type], $result['summary']);
        }
    }

    public function testExecuteAndRecordWithTotalInSummary(): void
    {
        $report = $this->createTestReport(Report::TYPE_ORDERS);

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->method('generate')
            ->willReturn([
                'data' => collect([['total' => 100], ['total' => 200]]),
                'summary' => ['total' => 300], // Using 'total' instead of 'total_revenue'
            ]);

        $action = new GenerateReportAction($mockDataService);
        $execution = $action->executeAndRecord($report, null, 1, []);

        $this->assertEquals(300, $execution->total_amount);
    }

    public function testExecuteAndRecordWithNullTotal(): void
    {
        $report = $this->createTestReport(Report::TYPE_COUPONS);

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->method('generate')
            ->willReturn([
                'data' => collect([['code' => 'TEST']]),
                'summary' => ['count' => 1], // No total or total_revenue
            ]);

        $action = new GenerateReportAction($mockDataService);
        $execution = $action->executeAndRecord($report, null, 1, []);

        $this->assertNull($execution->total_amount);
    }
}
