<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Post\Actions\ImportPostsAction;
use Modules\Post\Import\PostImport;
use Tests\Unit\Actions\ActionTestCase;

class ImportPostsActionTest extends ActionTestCase
{
    public function testExecuteImportsPostsFromFile(): void
    {
        Excel::fake();

        $file = UploadedFile::fake()->create('posts.xlsx');

        $action = app(ImportPostsAction::class);
        $action->execute($file);

        Excel::assertImported('posts.xlsx');
    }

    public function testExecuteAcceptsDifferentFileTypes(): void
    {
        Excel::fake();

        $xlsxFile = UploadedFile::fake()->create('posts.xlsx');
        $csvFile = UploadedFile::fake()->create('posts.csv');

        $action = app(ImportPostsAction::class);
        
        $action->execute($xlsxFile);
        Excel::assertImported('posts.xlsx');

        $action->execute($csvFile);
        Excel::assertImported('posts.csv');
    }

    public function testExecuteDoesNotThrowForEmptyFile(): void
    {
        Excel::fake();

        $file = UploadedFile::fake()->create('empty.xlsx');

        $action = app(ImportPostsAction::class);
        
        // Should not throw an exception
        $action->execute($file);
        
        $this->assertTrue(true); // Test passes if we reach this point
        Excel::assertImported('empty.xlsx');
    }

    public function testExecuteCallsExcelImport(): void
    {
        Excel::fake();

        $file = UploadedFile::fake()->create('posts.xlsx');

        $action = app(ImportPostsAction::class);
        $action->execute($file);

        // Verify that Excel import was triggered
        Excel::assertImported('posts.xlsx');
    }
}
