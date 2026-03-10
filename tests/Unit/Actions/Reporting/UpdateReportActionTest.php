<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Reporting;

use Modules\Reporting\Actions\CreateReportAction;
use Modules\Reporting\Actions\UpdateReportAction;
use Modules\Reporting\DTOs\ReportDTO;
use Modules\Reporting\Models\Report;
use Tests\Unit\Actions\ActionTestCase;

class UpdateReportActionTest extends ActionTestCase
{
    private function createTestReport(array $overrides = []): Report
    {
        $dto = new ReportDTO(
            name: $overrides['name'] ?? 'Original Report',
            slug: $overrides['slug'] ?? 'original-report',
            description: $overrides['description'] ?? 'Original description',
            type: $overrides['type'] ?? Report::TYPE_SALES,
            format: $overrides['format'] ?? Report::FORMAT_HTML,
            filters: $overrides['filters'] ?? null,
            columns: $overrides['columns'] ?? null,
            grouping: $overrides['grouping'] ?? null,
            sorting: $overrides['sorting'] ?? null,
            createdBy: $overrides['created_by'] ?? 1,
            isTemplate: $overrides['is_template'] ?? false,
            isPublic: $overrides['is_public'] ?? false,
            sortOrder: $overrides['sort_order'] ?? 0,
        );

        return app(CreateReportAction::class)->execute($dto);
    }

    public function testExecuteUpdatesReportName(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportDTO(
            name: 'Updated Report Name',
            slug: 'original-report',
            description: 'Original description',
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

        $action = app(UpdateReportAction::class);
        $result = $action->execute($report, $dto);

        $this->assertEquals('Updated Report Name', $result->name);
        $this->assertEquals('original-report', $result->slug); // Unchanged
    }

    public function testExecuteUpdatesReportDescription(): void
    {
        $report = $this->createTestReport();

        $dto = new ReportDTO(
            name: 'Original Report',
            slug: 'original-report',
            description: 'Updated description',
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

        $action = app(UpdateReportAction::class);
        $result = $action->execute($report, $dto);

        $this->assertEquals('Updated description', $result->description);
    }

    public function testExecuteUpdatesReportFormat(): void
    {
        $report = $this->createTestReport(['format' => Report::FORMAT_HTML]);

        $dto = new ReportDTO(
            name: 'Original Report',
            slug: 'original-report',
            description: null,
            type: Report::TYPE_SALES,
            format: Report::FORMAT_PDF,
            filters: null,
            columns: null,
            grouping: null,
            sorting: null,
            createdBy: 1,
            isTemplate: false,
            isPublic: false,
            sortOrder: 0,
        );

        $action = app(UpdateReportAction::class);
        $result = $action->execute($report, $dto);

        $this->assertEquals(Report::FORMAT_PDF, $result->format);
    }

    public function testExecuteUpdatesFilters(): void
    {
        $report = $this->createTestReport(['filters' => ['date_from' => '2024-01-01']]);

        $newFilters = ['date_from' => '2024-06-01', 'date_to' => '2024-06-30', 'status' => ['completed']];
        $dto = new ReportDTO(
            name: 'Original Report',
            slug: 'original-report',
            description: null,
            type: Report::TYPE_SALES,
            format: Report::FORMAT_HTML,
            filters: $newFilters,
            columns: null,
            grouping: null,
            sorting: null,
            createdBy: 1,
            isTemplate: false,
            isPublic: false,
            sortOrder: 0,
        );

        $action = app(UpdateReportAction::class);
        $result = $action->execute($report, $dto);

        $this->assertEquals($newFilters, $result->filters);
    }

    public function testExecuteUpdatesVisibility(): void
    {
        $report = $this->createTestReport(['is_public' => false]);

        $dto = new ReportDTO(
            name: 'Original Report',
            slug: 'original-report',
            description: null,
            type: Report::TYPE_SALES,
            format: Report::FORMAT_HTML,
            filters: null,
            columns: null,
            grouping: null,
            sorting: null,
            createdBy: 1,
            isTemplate: false,
            isPublic: true,
            sortOrder: 0,
        );

        $action = app(UpdateReportAction::class);
        $result = $action->execute($report, $dto);

        $this->assertTrue($result->is_public);
    }

    public function testExecuteUpdatesTemplateStatus(): void
    {
        $report = $this->createTestReport(['is_template' => false]);

        $dto = new ReportDTO(
            name: 'Original Report',
            slug: 'original-report',
            description: null,
            type: Report::TYPE_SALES,
            format: Report::FORMAT_HTML,
            filters: null,
            columns: null,
            grouping: null,
            sorting: null,
            createdBy: 1,
            isTemplate: true,
            isPublic: false,
            sortOrder: 0,
        );

        $action = app(UpdateReportAction::class);
        $result = $action->execute($report, $dto);

        $this->assertTrue($result->is_template);
    }

    public function testExecuteUpdatesSortOrder(): void
    {
        $report = $this->createTestReport(['sort_order' => 0]);

        $dto = new ReportDTO(
            name: 'Original Report',
            slug: 'original-report',
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
            sortOrder: 100,
        );

        $action = app(UpdateReportAction::class);
        $result = $action->execute($report, $dto);

        $this->assertEquals(100, $result->sort_order);
    }

    public function testExecuteUpdatesMultipleFields(): void
    {
        $report = $this->createTestReport([
            'name' => 'Old Name',
            'description' => 'Old description',
            'format' => Report::FORMAT_HTML,
        ]);

        $dto = new ReportDTO(
            name: 'New Name',
            slug: 'original-report',
            description: 'New description',
            type: Report::TYPE_PRODUCTS,
            format: Report::FORMAT_EXCEL,
            filters: ['category' => ['electronics']],
            columns: ['name', 'price', 'stock'],
            grouping: null,
            sorting: ['price' => 'desc'],
            createdBy: 1,
            isTemplate: true,
            isPublic: true,
            sortOrder: 50,
        );

        $action = app(UpdateReportAction::class);
        $result = $action->execute($report, $dto);

        $this->assertEquals('New Name', $result->name);
        $this->assertEquals('New description', $result->description);
        $this->assertEquals(Report::TYPE_PRODUCTS, $result->type);
        $this->assertEquals(Report::FORMAT_EXCEL, $result->format);
        $this->assertTrue($result->is_template);
        $this->assertTrue($result->is_public);
        $this->assertEquals(50, $result->sort_order);
    }

    public function testExecutePersistsChangesToDatabase(): void
    {
        $report = $this->createTestReport(['name' => 'Original']);

        $dto = new ReportDTO(
            name: 'Database Updated',
            slug: 'original-report',
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

        $action = app(UpdateReportAction::class);
        $action->execute($report, $dto);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'name' => 'Database Updated',
        ]);

        $this->assertDatabaseMissing('reports', [
            'id' => $report->id,
            'name' => 'Original',
        ]);
    }
}
