<?php

namespace Modules\Settings\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Settings\Models\Setting;

class SettingsRepository extends Repository
{
    public \Illuminate\Database\Eloquent\Model $model = Setting::class;

    /**
     * @return mixed
     */
    public function findFirst(): mixed
    {
        return $this->model::first();
    }
}