<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Repository\SettingsRepository;

class GetSettingsAction
{
    private SettingsRepository $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        $firstSetting = $this->repository->findFirst();

        // If no settings exist, return empty array with defaults
        if (! $firstSetting) {
            return [
                'id' => null,
                'description' => null,
                'short_des' => null,
                'logo' => null,
                'photo' => null,
                'address' => null,
                'phone' => null,
                'email' => null,
                'site-name' => null,
                'active_template' => 'default',
                'fb_app_id' => null,
                'keywords' => null,
                'google-site-verification' => null,
                'longitude' => null,
                'latitude' => null,
                'google_map_api_key' => null,
            ];
        }

        // Convert Setting model to array
        return $firstSetting->toArray();
    }
}
