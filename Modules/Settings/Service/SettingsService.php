<?php

declare(strict_types=1);

namespace Modules\Settings\Service;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Service\CoreService;
use Modules\Settings\Repository\SettingsRepository;

class SettingsService extends CoreService
{
    private SettingsRepository $settings_repository;

    public function __construct(SettingsRepository $settings_repository)
    {
        parent::__construct($settings_repository);
        $this->settings_repository = $settings_repository;
    }

    /**
     * Get the first settings data.
     */
    public function getData(): object
    {
        return $this->settings_repository->findFirst();
    }

    /**
     * Update settings with new data and possibly new media files.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateWithMedia(int $id, array $data): Model
    {
        $setting = $this->settings_repository->findById($id);
        $setting->update($data);
        if (request()->hasFile('images')) {
            $setting->clearMediaCollection('settings');
            $setting->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder): void {
                    $fileAdder->preservingOriginal()->toMediaCollection('settings');
                });
        }

        return $setting;
    }
}
