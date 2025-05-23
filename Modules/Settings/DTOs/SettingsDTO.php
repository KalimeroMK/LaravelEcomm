<?php

declare(strict_types=1);

namespace Modules\Settings\DTOs;

class SettingsDTO
{
    public array $settings;

    public function __construct($settings)
    {
        $this->settings = $settings->toArray();
    }
}
