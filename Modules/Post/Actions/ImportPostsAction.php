<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Post\Import\PostImport;

readonly class ImportPostsAction
{
    public function execute(UploadedFile $file): void
    {
        Excel::import(new PostImport, $file);
    }
}
