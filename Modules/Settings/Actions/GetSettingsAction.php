<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\DTOs\SettingsDTO;
use Modules\Settings\Repository\SettingsRepository;

class GetSettingsAction
{
    private SettingsRepository $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): SettingsDTO
    {
        $settings = $this->repository->findAll();

        return SettingsDTO::fromArray($settings->first());
    }
}
