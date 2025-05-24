<?php

declare(strict_types=1);

namespace Modules\Settings\Repository;

use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Settings\Models\Setting;

class SettingsRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Setting::class);
    }

    /**
     * Retrieve the first settings record.
     */
    public function findFirst(): ?Setting
    {
        return (new $this->modelClass)->first();
    }
}
