<?php

declare(strict_types=1);

namespace Modules\Settings\DTOs;

use Illuminate\Http\Request;

readonly class SettingsDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $description = null,
        public ?string $short_des = null,
        public ?string $logo = null,
        public ?string $photo = null,
        public ?string $address = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $site_name = null,
        public ?string $fb_app_id = null,
        public ?string $keywords = null,
        public ?string $google_site_verification = null,
        public ?string $longitude = null,
        public ?string $latitude = null,
        public ?string $google_map_api_key = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['description'] ?? null,
            $data['short_des'] ?? null,
            $data['logo'] ?? null,
            $data['photo'] ?? null,
            $data['address'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['site-name'] ?? null,
            $data['fb_app_id'] ?? null,
            $data['keywords'] ?? null,
            $data['google-site-verification'] ?? null,
            $data['longitude'] ?? null,
            $data['latitude'] ?? null,
            $data['google_map_api_key'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }
}
