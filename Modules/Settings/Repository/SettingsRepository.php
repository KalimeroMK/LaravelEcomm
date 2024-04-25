<?php

namespace Modules\Settings\Repository;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Repositories\Repository;
use Modules\Settings\Models\Setting;

class SettingsRepository extends Repository
{
    public Model $model = Setting::class;

    /**
     * @return mixed
     */
    public function findFirst(): mixed
    {
        return $this->model::first();
    }
}