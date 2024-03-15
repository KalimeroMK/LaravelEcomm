<?php

namespace Modules\Product\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Product\Models\Product;

class Products implements ToCollection, WithHeadingRow
{
    /**
     * @param  Collection  $collection
     * @return Product
     */
    function collection(Collection $collection): string
    {
        foreach ($collection as $item) {
            Product::Create([
                'title' => $item['title'],
                'slug' => $item['slug'],
                'summary' => $item['summary'],
                'description' => $item['description'],
                'status' => $item['status'],
                'quote' => $item['price'],
                'discount' => $item['discount'],
                'id_featured' => $item['is_featured'],
                'color' => $item['color'],
                'special_price' => $item['special_price'],
                'special_price_start' => $item['special_price_start'],
                'special_price_end' => $item['special_price_end'],


            ]);
        }

        return 'Update done ';
    }
}
