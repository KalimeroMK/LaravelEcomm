<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Repository\SettingsRepository;

class UpdateSettingsAction
{
    private SettingsRepository $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, array $data): void
    {
        $this->repository->update($id, $data);
    }
}
