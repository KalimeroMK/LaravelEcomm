<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Models\Setting;
use Modules\Settings\Repository\SettingsRepository;

readonly class FindSettingAction
{
    public function __construct(private SettingsRepository $repository) {}

    public function execute(?int $id = null): ?Setting
    {
        if ($id) {
            return $this->repository->findById($id);
        }

        return Setting::first();
    }
}
