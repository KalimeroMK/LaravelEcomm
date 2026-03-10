<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Actions\ExportProductsAction;
use Modules\Product\Exports\Products;
use Tests\Unit\Actions\ActionTestCase;

class ExportProductsActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function testExecuteExportsProductsWithDefaultFilename(): void
    {
        Excel::fake();

        $action = app(ExportProductsAction::class);
        $result = $action->execute();

        Excel::assertStored('exports/products.xlsx', function (Products $export) {
            return true;
        });

        $this->assertStringContainsString('products.xlsx', $result);
        $this->assertStringContainsString('exports', $result);
    }

    public function testExecuteExportsProductsWithCustomFilename(): void
    {
        Excel::fake();

        $action = app(ExportProductsAction::class);
        $result = $action->execute('custom-products.xlsx');

        Excel::assertStored('exports/custom-products.xlsx', function (Products $export) {
            return true;
        });

        $this->assertStringContainsString('custom-products.xlsx', $result);
    }

    public function testExecuteReturnsCorrectFilePath(): void
    {
        Excel::fake();

        $action = app(ExportProductsAction::class);
        $result = $action->execute('test-export.xlsx');

        $expectedPath = storage_path('app/exports/test-export.xlsx');
        $this->assertEquals($expectedPath, $result);
    }
}
