<?php

namespace Modules\Product\Import;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Post\Models\Post;

class Products implements ToCollection, WithHeadingRow
{
    /**
     * @param  Collection  $collection
     *
     * @return string
     */
    public
    function collection(
        Collection $collection
    ): string {
        Schema::disableForeignKeyConstraints();
        foreach ($collection as $item) {
            Post::Create([
                'title'       => $item['title'],
                'slug'        => $item['slug'],
                'summary'     => $item['summary'],
                'description' => $item['summary'],
                'stock'       => $item['stock'],
                'photo'       => $item['photo'],
                'size'        => $item['size'],
                'status'      => $item['status'],
                'condition'   => $item['condition'],
                'price'       => $item['price'],
                'is_featured' => $item['is_featured'],
                'brand_id'    => $item['brand_id'],
                'color'       => $item['color'],
            
            ]);
        }
        Schema::enableForeignKeyConstraints();
        
        return 'Update done ';
    }
}