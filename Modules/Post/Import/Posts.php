<?php

namespace Modules\Post\Import;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Post\Models\Post;

class Posts implements ToCollection, WithHeadingRow
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
                'quote'       => $item['quote'],
                'photo'       => $item['photo'],
                'tags'        => $item['tags'],
                'status'      => $item['status'],
            ]);
        }
        Schema::enableForeignKeyConstraints();
        
        return 'Update done ';
    }
}