<?php

declare(strict_types=1);

namespace Modules\Post\Export;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Post\Models\Post;
use Modules\Post\Repository\PostRepository;

class PostExport implements FromCollection, WithHeadings, WithMapping
{
    private PostRepository $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get the headings for the export.
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
            'quote',
            'photo',
            'tags',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Get the collection of posts for the export.
     *
     * @return Collection<int, Post>
     */
    public function collection(): Collection
    {
        return $this->repository->findAll();
    }

    /**
     * Map the data for each post.
     *
     * @param  Post  $row
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
            $row->quote ?? 'no data',
            $row->photo ?? 'no data',
            $row->tags ?? 'no data',
            $row->status ?? 'no data',
            Carbon::parse($row->created_at)->toFormattedDateString(),
            Carbon::parse($row->updated_at)->toFormattedDateString(),
        ];
    }
}
