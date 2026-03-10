<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Reporting;

use Modules\Reporting\Actions\CreateReportAction;
use Modules\Reporting\DTOs\ReportDTO;
use Modules\Reporting\Models\Report;
use Tests\Unit\Actions\ActionTestCase;

class CreateReportActionTest extends ActionTestCase
{
    public function testExecuteCreatesReport(): void
    {
        $dto = new ReportDTO(
            name: 'Monthly Sales Report',
            slug: 'monthly-sales-report',
            description: 'Monthly sales performance report',
            type: Report::TYPE_SALES,
            format: Report::FORMAT_HTML,
            filters: ['date_from' => '2024-01-01', 'date_to' => '2024-01-31'],
            columns: ['date', 'order_id', 'total', 'status'],
            grouping: null,
            sorting: ['date' => 'desc'],
            createdBy: 1,
            isTemplate: false,
            isPublic: true,
            sortOrder: 0,
        );

        $action = app(CreateReportAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Report::class, $result);
        $this->assertEquals('Monthly Sales Report', $result->name);
        $this->assertEquals('monthly-sales-report', $result->slug);
        $this->assertEquals('Monthly sales performance report', $result->description);
        $this->assertEquals(Report::TYPE_SALES, $result->type);
        $this->assertEquals(Report::FORMAT_HTML, $result->format);
    }

    public function testExecuteCreatesReportWithMinimumData(): void
    {
        $dto = new ReportDTO(
            name: 'Simple Report',
            slug: 'simple-report',
            description: null,
            type: Report::TYPE_ORDERS,
            format: Report::FORMAT_CSV,
            filters: null,
            columns: null,
            grouping: null,
            sorting: null,
            createdBy: 1,
            isTemplate: false,
            isPublic: false,
            sortOrder: 0,
        );

        $action = app(CreateReportAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Report::class, $result);
        $this->assertEquals('Simple Report', $result->name);
        $this->assertEquals(Report::TYPE_ORDERS, $result->type);
    }

    public function testExecuteCreatesTemplateReport(): void
    {
        $dto = new ReportDTO(
            name: 'Template Report',
            slug: 'template-report',
            description: null,
            type: Report::TYPE_PRODUCTS,
            format: Report::FORMAT_PDF,
            filters: null,
            columns: null,
            grouping: null,
            sorting: null,
            createdBy: 1,
            isTemplate: true,
            isPublic: false,
            sortOrder: 10,
        );

        $action = app(CreateReportAction::class);
        $result = $action->execute($dto);

        $this->assertTrue($result->is_template);
        $this->assertEquals(10, $result->sort_order);
    }

    public function testExecuteCreatesMultipleReports(): void
    {
        $dto1 = new ReportDTO(
            name: 'Sales Report',
            slug: 'sales-report',
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

        $dto2 = new ReportDTO(
            name: 'Customer Report',
            slug: 'customer-report',
            description: null,
            type: Report::TYPE_CUSTOMERS,
            format: Report::FORMAT_EXCEL,
            filters: null,
            columns: null,
            grouping: null,
            sorting: null,
            createdBy: 1,
            isTemplate: false,
            isPublic: false,
            sortOrder: 0,
        );

        $action = app(CreateReportAction::class);
        $result1 = $action->execute($dto1);
        $result2 = $action->execute($dto2);

        $this->assertNotEquals($result1->id, $result2->id);
        $this->assertDatabaseHas('reports', ['slug' => 'sales-report']);
        $this->assertDatabaseHas('reports', ['slug' => 'customer-report']);
    }

    public function testExecuteCreatesReportWithAllTypes(): void
    {
        $types = [
            Report::TYPE_SALES,
            Report::TYPE_PRODUCTS,
            Report::TYPE_CUSTOMERS,
            Report::TYPE_INVENTORY,
            Report::TYPE_ORDERS,
            Report::TYPE_COUPONS,
            Report::TYPE_REVENUE,
            Report::TYPE_TAX,
        ];

        $action = app(CreateReportAction::class);

        foreach ($types as $index => $type) {
            $dto = new ReportDTO(
                name: ucfirst($type) . ' Report',
                slug: $type . '-report',
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
                sortOrder: $index,
            );

            $result = $action->execute($dto);
            $this->assertEquals($type, $result->type);
        }

        $this->assertDatabaseCount('reports', count($types));
    }

    public function testExecuteSavesReportToDatabase(): void
    {
        $dto = new ReportDTO(
            name: 'Database Test Report',
            slug: 'database-test-report',
            description: 'Testing database persistence',
            type: Report::TYPE_REVENUE,
            format: Report::FORMAT_PDF,
            filters: ['status' => ['completed']],
            columns: ['date', 'revenue'],
            grouping: ['date'],
            sorting: ['revenue' => 'desc'],
            createdBy: 1,
            isTemplate: false,
            isPublic: true,
            sortOrder: 5,
        );

        $action = app(CreateReportAction::class);
        $action->execute($dto);

        $this->assertDatabaseHas('reports', [
            'name' => 'Database Test Report',
            'slug' => 'database-test-report',
            'type' => Report::TYPE_REVENUE,
            'format' => Report::FORMAT_PDF,
            'is_public' => true,
            'sort_order' => 5,
        ]);
    }
}
