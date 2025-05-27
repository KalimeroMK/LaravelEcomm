<?php

declare(strict_types=1);

namespace Modules\Post\Import;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Post\Models\Post;
use Throwable;

class PostImport implements ToModel, WithHeadingRow
{
    /**
     * @return Post|null
     */
    public function model(array $row)
    {
        try {
            return new Post([
                'title' => $row['title'] ?? '',
                'slug' => $row['slug'] ?? '',
                'summary' => $row['summary'] ?? '',
                'description' => $row['description'] ?? null,
                'photo' => $row['photo'] ?? null,
                'status' => $row['status'] ?? 'inactive',
                'user_id' => $row['user_id'] ?? 1,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to import post row', ['row' => $row, 'error' => $e->getMessage()]);

            return null;
        }
    }
}
