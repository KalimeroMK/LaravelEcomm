<?php

namespace Modules\Settings\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Settings\Models\Setting;

class SettingsRepository extends Repository
{
    public $model = Setting::class;

    public function findFirst(): object
    {
        return $this->model::first();
    }
}
