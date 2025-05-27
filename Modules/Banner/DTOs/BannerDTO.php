<?php

declare(strict_types=1);

namespace Modules\Banner\DTOs;

use Illuminate\Http\Request;
use Modules\Banner\Http\Requests\Api\Store as ApiStore;
use Modules\Banner\Http\Requests\Api\Update as ApiUpdate;
use Modules\Banner\Http\Requests\Store;
use Modules\Banner\Http\Requests\Update;

readonly class BannerDTO
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?string $slug,
        public ?string $description,
        public ?string $status,
        public array $images = []
    ) {}

    /**
     * Accepts Store, Update, Api\Store, Api\Update or Request.
     */
    public static function fromRequest(Store|Update|ApiStore|ApiUpdate|Request $request): self
    {
        return self::fromArray($request->validated());
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['slug'] ?? null,
            $data['description'] ?? null,
            $data['status'] ?? null,
            $data['images'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'images' => $this->images,
        ];
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->title,
            $this->slug,
            $this->description,
            $this->status,
            $this->images
        );
    }
}
