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
            id: $data['id'] ?? null,
            description: $data['description'] ?? null,
            short_des: $data['short_des'] ?? null,
            logo: $data['logo'] ?? null,
            photo: $data['photo'] ?? null,
            address: $data['address'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            site_name: $data['site_name'] ?? null,
            fb_app_id: $data['fb_app_id'] ?? null,
            keywords: $data['keywords'] ?? null,
            google_site_verification: $data['google_site_verification'] ?? null,
            longitude: $data['longitude'] ?? null,
            latitude: $data['latitude'] ?? null,
            google_map_api_key: $data['google_map_api_key'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }
}
