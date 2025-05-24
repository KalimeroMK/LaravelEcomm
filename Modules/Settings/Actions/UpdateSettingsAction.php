<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Repository\SettingsRepository;

readonly class UpdateSettingsAction
{
    public function __construct(private SettingsRepository $repository)
    {
    }

    public function execute(int $id, array $data): void
    {
        $this->repository->update($id, $data);
    }
}
