<?php

namespace Modules\Product\Export;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Product\Models\Product;

class Products implements FromCollection, WithHeadings, WithMapping
{
    
    public function headings(): array
    {
        return [
            '#',
            'title',
            'slug',
            'summary',
            'description',
            'photo',
            'stock',
            'size',
            'condition',
            'status',
            'price',
            'discount',
            'is_featured',
            'brand_id',
            'color',
            'created_at',
            'updated_at',
        
        ];
    }
    
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Product::with('brand')->get();
    }
    
    public function map($row): array
    {
        return [
            $row->id,
            $row->title,
            $row->slug,
            $row->summary ?? 'no data',
            $row->description ?? 'no data',
            $row->photo ?? 'no data',
            $row->stock ?? 'no data',
            $row->size ?? 'no data',
            $row->condition ?? 'no data',
            $row->status ?? 'no data',
            $row->price ?? 'no data',
            $row->discount ?? 'no data',
            $row->is_featured ?? 'no data',
            $row->brand->title ?? 'no data',
            $row->color ?? 'no data',
            Carbon::parse($row->created_at)->toFormattedDateString() ?? '/',
            Carbon::parse($row->update_at)->toFormattedDateString() ?? '/',
        ];
    }
}
