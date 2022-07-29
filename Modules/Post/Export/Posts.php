<?php

namespace Modules\Post\Export;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Post\Models\Post;

class Posts implements FromCollection, WithHeadings, WithMapping
{
    
    public function headings(): array
    {
        return [
            '#',
            'title',
            'slug',
            'summary',
            'description',
            'quote',
            'photo',
            'tags',
            'status',
            'created_at',
            'updated_at',
        
        ];
    }
    
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Post::all();
    }
    
    public function map($row): array
    {
        return [
            $row->id,
            $row->title,
            $row->slug,
            $row->summary ?? 'no data',
            $row->description ?? 'no data',
            $row->quote ?? 'no data',
            $row->photo ?? 'no data',
            $row->tags ?? 'no data',
            $row->status ?? 'no data',
            Carbon::parse($row->created_at)->toFormattedDateString() ?? '/',
            Carbon::parse($row->update_at)->toFormattedDateString() ?? '/',
        ];
    }
}
