<?php

namespace Modules\Settings\Service;

use Modules\Admin\Models\Setting;
use Modules\Settings\Repository\SettingsRepository;

class SettingsService
{
    private SettingsRepository $settings_repository;
    
    public function __construct(SettingsRepository $settings_repository)
    {
        $this->settings_repository = $settings_repository;
    }
    
    public function index()
    {
        return $this->settings_repository->findFirst();
    }
    
    public function update($data)
    {
        $id = Setting::first()->id;
        
        return $this->settings_repository->update($id, $data);
    }
    
}