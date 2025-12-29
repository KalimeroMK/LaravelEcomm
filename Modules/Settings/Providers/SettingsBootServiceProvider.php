<?php

declare(strict_types=1);

namespace Modules\Settings\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;

class SettingsBootServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Apply email settings to mail config
        $this->applyEmailSettings();
    }

    /**
     * Apply email settings from database to mail configuration
     */
    protected function applyEmailSettings(): void
    {
        try {
            $setting = \Modules\Settings\Models\Setting::first();
            if (! $setting || ! $setting->email_settings) {
                return;
            }

            $emailSettings = $setting->email_settings;

            // Apply mail driver
            if (isset($emailSettings['mail_driver'])) {
                config(['mail.default' => $emailSettings['mail_driver']]);
            }

            // Apply SMTP settings
            if (isset($emailSettings['mail_host'])) {
                config(['mail.mailers.smtp.host' => $emailSettings['mail_host']]);
            }
            if (isset($emailSettings['mail_port'])) {
                config(['mail.mailers.smtp.port' => $emailSettings['mail_port']]);
            }
            if (isset($emailSettings['mail_username'])) {
                config(['mail.mailers.smtp.username' => $emailSettings['mail_username']]);
            }
            if (isset($emailSettings['mail_password'])) {
                config(['mail.mailers.smtp.password' => $emailSettings['mail_password']]);
            }
            if (isset($emailSettings['mail_encryption'])) {
                config(['mail.mailers.smtp.encryption' => $emailSettings['mail_encryption']]);
            }

            // Apply from address
            if (isset($emailSettings['mail_from_address'])) {
                config(['mail.from.address' => $emailSettings['mail_from_address']]);
            }
            if (isset($emailSettings['mail_from_name'])) {
                config(['mail.from.name' => $emailSettings['mail_from_name']]);
            }
        } catch (Exception $e) {
            // Silently fail if settings table doesn't exist or settings are not available
            // This is important for migrations and initial setup
        }
    }
}
