<?php

namespace Modules\Settings\Service;

use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;
use Modules\Settings\Repository\SettingsRepository;

class SettingsService extends CoreService
{
    use ImageUpload;

    private SettingsRepository $settings_repository;

    public function __construct(SettingsRepository $settings_repository)
    {
        $this->settings_repository = $settings_repository;
    }

    public function getData()
    {
        return $this->settings_repository->findFirst();
    }

    public function update($id, $data)
    {
        return $this->settings_repository->update($id,
            collect($data)->except(['logo'])->toArray() + [
                'logo' => $this->verifyAndStoreImage($data['logo']),
            ]
        );
    }

}
