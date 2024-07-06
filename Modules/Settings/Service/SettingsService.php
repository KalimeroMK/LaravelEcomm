<?php

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
     *
     * @return object
     */
    public function getData(): object
    {
        return $this->settings_repository->findFirst();
    }

    /**
     * Update an existing banner with new data and possibly new media files.
     *
     * @param int $id The banner ID to update.
     * @param array<string, mixed> $data The data for updating the banner.
     * @return Model The updated banner model.
     */
    public function update(int $id, array $data): Model
    {
        $setting = $this->settings_repository->findById($id);

        $setting->update($data);

        // Check for new image uploads and handle them
        if (request()->hasFile('images')) {
            $setting->clearMediaCollection('settings'); // Optionally clear existing media
            $setting->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('settings');
                });
        }

        return $setting;
    }
}
