<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Actions\ImportProductsAction;
use Modules\Product\Exports\Products;
use RuntimeException;
use Tests\Unit\Actions\ActionTestCase;

class ImportProductsActionTest extends ActionTestCase
{
    public function testExecuteThrowsExceptionWhenNoFileProvided(): void
    {
        $action = app(ImportProductsAction::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No file provided.');

        $action->execute(null);
    }

    public function testExecuteThrowsExceptionForMultipleFiles(): void
    {
        $action = app(ImportProductsAction::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Multiple files uploaded. Please upload only one file.');

        $action->execute(['file1.xlsx', 'file2.xlsx']);
    }

    public function testExecuteImportsProductsSuccessfully(): void
    {
        Excel::fake();

        $file = UploadedFile::fake()->create('products.xlsx');

        $action = app(ImportProductsAction::class);

        // Should not throw any exception
        $action->execute($file);

        Excel::assertImported('products.xlsx', function (Products $import) {
            return true;
        });
    }

    public function testExecuteWrapsExceptionInRuntimeException(): void
    {
        Excel::shouldReceive('import')
            ->once()
            ->andThrow(new \Exception('Import failed'));

        $file = UploadedFile::fake()->create('products.xlsx');
        $action = app(ImportProductsAction::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to import products: Import failed');

        $action->execute($file);
    }
}
