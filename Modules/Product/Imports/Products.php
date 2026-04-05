<?php

declare(strict_types=1);

namespace Modules\Product\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Product\Models\Product;

class Products implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): string
    {
        foreach ($collection as $item) {
            Product::create([
                'title' => $item['title'],
                'slug' => $item['slug'],
                'summary' => $item['summary'],
                'description' => $item['description'],
                'status' => $item['status'] ?? 'inactive',
                'price' => (float) ($item['price'] ?? 0),
                'discount' => (float) ($item['discount'] ?? 0),
                'is_featured' => (bool) ($item['is_featured'] ?? false),
                'color' => $item['color'] ?? null,
                'special_price' => $item['special_price'] ?? null,
                'special_price_start' => $item['special_price_start'] ?? null,
                'special_price_end' => $item['special_price_end'] ?? null,
            ]);
        }

        return 'Update done ';
    }
}
