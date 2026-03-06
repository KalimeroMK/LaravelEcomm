<?php

declare(strict_types=1);

namespace Modules\Reporting\DTOs;

readonly class ReportDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public string $type,
        public string $format,
        public ?array $filters,
        public ?array $columns,
        public ?array $grouping,
        public ?array $sorting,
        public int $createdBy,
        public bool $isTemplate,
        public bool $isPublic,
        public int $sortOrder,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'] ?? \Illuminate\Support\Str::slug($data['name']),
            description: $data['description'] ?? null,
            type: $data['type'],
            format: $data['format'] ?? 'html',
            filters: $data['filters'] ?? null,
            columns: $data['columns'] ?? null,
            grouping: $data['grouping'] ?? null,
            sorting: $data['sorting'] ?? null,
            createdBy: $data['created_by'],
            isTemplate: $data['is_template'] ?? false,
            isPublic: $data['is_public'] ?? false,
            sortOrder: $data['sort_order'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $this->type,
            'format' => $this->format,
            'filters' => $this->filters,
            'columns' => $this->columns,
            'grouping' => $this->grouping,
            'sorting' => $this->sorting,
            'created_by' => $this->createdBy,
            'is_template' => $this->isTemplate,
            'is_public' => $this->isPublic,
            'sort_order' => $this->sortOrder,
        ];
    }
}
