<?php

declare(strict_types=1);

namespace Modules\Product\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Product\Models\Product;

class Products implements FromQuery, WithHeadings, WithMapping
{
    /**
     * Get the headers for the export.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            '#',
            'title',
            'slug',
            'summary',
            'description',
            'stock',
            'status',
            'price',
            'discount',
            'is_featured',
            'color',
            'special_price',
            'special_price_start',
            'special_price_end',
        ];
    }

    /**
     * Return the query for chunked export — avoids loading all rows into memory.
     */
    public function query(): Builder
    {
        return Product::query();
    }

    /**
     * Map the data for each product.
     *
     * @param  Product  $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->title,
            $row->slug,
            $row->summary ?? 'no data',
            $row->description ?? 'no data',
            $row->stock ?? 'no data',
            $row->status ?? 'no data',
            $row->price ?? 'no data',
            $row->discount ?? 'no data',
            $row->is_featured ?? 'no data',
            $row->brand_id ?? 'no data',
            $row->color ?? 'no data',
            $row->special_price ?? 'no data',
            $row->special_price_start ?? 'no data',
            $row->special_price_end ?? 'no data',
        ];
    }
}
