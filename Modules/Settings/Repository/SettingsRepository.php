<?php

namespace Modules\Settings\Repository;

use Modules\Admin\Models\Setting;
use Modules\Core\Repositories\Repository;

class SettingsRepository extends Repository
{
    public $model = Setting::class;
    
    /**
     * @return mixed
     */
    public function findFirst(): mixed
    {
        return $this->model::first();
    }
}