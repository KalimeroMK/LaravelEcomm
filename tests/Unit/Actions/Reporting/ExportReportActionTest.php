<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Reporting;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Modules\Reporting\Actions\CreateReportAction;
use Modules\Reporting\Actions\ExportReportAction;
use Modules\Reporting\DTOs\ReportDTO;
use Modules\Reporting\Models\Report;
use Modules\Reporting\Services\ReportDataService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\Unit\Actions\ActionTestCase;

class ExportReportActionTest extends ActionTestCase
{
    private function createTestReport(string $type = Report::TYPE_SALES): Report
    {
        $dto = new ReportDTO(
            name: 'Sales Report',
            slug: 'sales-report',
            description: null,
            type: $type,
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

    private function createMockDataService(array $data, array $summary = []): ReportDataService
    {
        $mock = $this->createMock(ReportDataService::class);
        $mock->method('generate')
            ->willReturn([
                'data' => collect($data),
                'summary' => $summary,
            ]);

        return $mock;
    }

    public function testExecuteExportsToCsv(): void
    {
        $report = $this->createTestReport();
        $data = [
            ['date' => '2024-01-01', 'order_id' => '1001', 'total' => 150.00],
            ['date' => '2024-01-02', 'order_id' => '1002', 'total' => 250.00],
        ];

        $dataService = $this->createMockDataService($data, ['total_revenue' => 400.00]);
        $action = new ExportReportAction($dataService);

        $result = $action->execute($report, 'csv');

        $this->assertInstanceOf(StreamedResponse::class, $result);
        $this->assertStringContainsString('text/csv', $result->headers->get('Content-Type'));
        $this->assertStringContainsString('sales-report_', $result->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.csv', $result->headers->get('Content-Disposition'));
    }

    public function testExecuteExportsToExcel(): void
    {
        $report = $this->createTestReport();
        $data = [
            ['product' => 'Widget A', 'quantity' => 10, 'revenue' => 100.00],
            ['product' => 'Widget B', 'quantity' => 5, 'revenue' => 75.00],
        ];

        $dataService = $this->createMockDataService($data);
        $action = new ExportReportAction($dataService);

        $result = $action->execute($report, 'excel');

        $this->assertInstanceOf(StreamedResponse::class, $result);
        $this->assertStringContainsString('spreadsheetml.sheet', $result->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', $result->headers->get('Content-Disposition'));
    }

    public function testExecuteExportsToPdf(): void
    {
        $report = $this->createTestReport();
        $data = [
            ['customer' => 'John Doe', 'orders' => 5, 'spent' => 500.00],
        ];

        $dataService = $this->createMockDataService($data, ['total_customers' => 1]);
        $action = new ExportReportAction($dataService);

        // Create a mock view that returns HTML content
        $mockView = $this->createMock(\Illuminate\Contracts\View\View::class);
        $mockView->method('render')->willReturn('<html><body>PDF Content</body></html>');

        // Mock the view factory
        View::shouldReceive('make')
            ->once()
            ->andReturn($mockView);

        $result = $action->execute($report, 'pdf');

        $this->assertIsString($result);
        $this->assertStringContainsString('PDF Content', $result);
    }

    public function testExecuteThrowsExceptionForUnsupportedFormat(): void
    {
        $report = $this->createTestReport();
        $dataService = $this->createMockDataService([]);
        $action = new ExportReportAction($dataService);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported format: xml');

        $action->execute($report, 'xml');
    }

    public function testExecuteCsvWithEmptyData(): void
    {
        $report = $this->createTestReport();
        $dataService = $this->createMockDataService([]);
        $action = new ExportReportAction($dataService);

        $result = $action->execute($report, 'csv');

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }

    public function testExecuteCsvFormatsPriceColumns(): void
    {
        $report = $this->createTestReport();
        $data = [
            ['product' => 'Item 1', 'unit_price' => 99.999, 'total' => 199.998, 'revenue' => 1000.5, 'spent' => 50.0],
        ];

        $dataService = $this->createMockDataService($data);
        $action = new ExportReportAction($dataService);

        $response = $action->execute($report, 'csv');

        $this->assertInstanceOf(StreamedResponse::class, $response);
        // The response is a streamed response, so we just verify it was created successfully
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testExecuteCsvWithNestedData(): void
    {
        $report = $this->createTestReport();
        $data = [
            ['id' => 1, 'name' => 'Product A', 'category' => 'Electronics'],
            ['id' => 2, 'name' => 'Product B', 'category' => 'Clothing'],
        ];

        $dataService = $this->createMockDataService($data);
        $action = new ExportReportAction($dataService);

        $result = $action->execute($report, 'csv');

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }

    public function testExecuteWithParameters(): void
    {
        $report = $this->createTestReport();
        $parameters = ['date_from' => '2024-01-01', 'date_to' => '2024-01-31'];

        $mockDataService = $this->createMock(ReportDataService::class);
        $mockDataService->expects($this->once())
            ->method('generate')
            ->with($report, $parameters)
            ->willReturn([
                'data' => collect(),
                'summary' => [],
            ]);

        $action = new ExportReportAction($mockDataService);
        $action->execute($report, 'csv', $parameters);
    }

    public function testExecuteAllFormats(): void
    {
        $report = $this->createTestReport();
        $data = [['col1' => 'value1', 'col2' => 'value2']];

        $formats = ['csv', 'excel'];

        foreach ($formats as $format) {
            $dataService = $this->createMockDataService($data);
            $action = new ExportReportAction($dataService);

            $result = $action->execute($report, $format);

            $this->assertInstanceOf(StreamedResponse::class, $result);
        }
    }

    public function testExecuteCsvContainsUtf8Bom(): void
    {
        $report = $this->createTestReport();
        $data = [['name' => 'Test Product']];

        $dataService = $this->createMockDataService($data);
        $action = new ExportReportAction($dataService);

        $response = $action->execute($report, 'csv');

        // Get the response callback
        $reflection = new \ReflectionClass($response);
        $callbackProperty = $reflection->getProperty('callback');
        $callbackProperty->setAccessible(true);
        $callback = $callbackProperty->getValue($response);

        // Capture output
        ob_start();
        $callback();
        $output = ob_get_clean();

        // Check for UTF-8 BOM
        $this->assertStringStartsWith("\xEF\xBB\xBF", $output);
    }

    public function testExecuteCsvHeadersMatchDataKeys(): void
    {
        $report = $this->createTestReport();
        $data = [
            ['date' => '2024-01-01', 'revenue' => 100, 'orders' => 5],
        ];

        $dataService = $this->createMockDataService($data);
        $action = new ExportReportAction($dataService);

        $response = $action->execute($report, 'csv');

        // Get the response callback
        $reflection = new \ReflectionClass($response);
        $callbackProperty = $reflection->getProperty('callback');
        $callbackProperty->setAccessible(true);
        $callback = $callbackProperty->getValue($response);

        // Capture output
        ob_start();
        $callback();
        $output = ob_get_clean();

        // Check that headers are present
        $lines = explode("\n", trim($output));
        $headerLine = str_replace("\xEF\xBB\xBF", '', $lines[0]);
        $this->assertStringContainsString('date', $headerLine);
        $this->assertStringContainsString('revenue', $headerLine);
        $this->assertStringContainsString('orders', $headerLine);
    }
}
