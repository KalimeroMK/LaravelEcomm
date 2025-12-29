<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Models\Setting;
use Modules\Settings\Repository\SettingsRepository;

readonly class UpdateSettingsAction
{
    public function __construct(
        private SettingsRepository $repository
    ) {}

    public function execute(int $id, array $data): Setting
    {
        $setting = $this->repository->findById($id);

        // Handle file uploads if present
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                if ($image && $image->isValid()) {
                    $setting->addMediaFromRequest('images[]')
                        ->toMediaCollection('settings');
                }
            }
            unset($data['images']);
        }

        // Filter only fillable fields
        $fillable = $setting->getFillable();
        $filteredData = array_intersect_key($data, array_flip($fillable));

        // Update setting fields
        $setting->fill($filteredData);
        $setting->save();

        return $setting->fresh();
    }
}
