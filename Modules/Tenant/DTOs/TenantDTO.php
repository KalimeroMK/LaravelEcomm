<?php

declare(strict_types=1);

namespace Modules\Tenant\DTOs;

use Illuminate\Http\Request;
use Modules\Tenant\Models\Tenant;

readonly class TenantDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $domain,
        public string $database,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Tenant $existing = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['name'] ?? $existing?->name ?? '',
            $data['domain'] ?? $existing?->domain ?? '',
            $data['database'] ?? $existing?->database ?? '',
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? '',
            $data['domain'] ?? '',
            $data['database'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'domain' => $this->domain,
            'database' => $this->database,
        ];
    }
}
