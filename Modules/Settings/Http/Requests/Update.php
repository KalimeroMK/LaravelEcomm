<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        // Check if this is a specific settings route (email, payment, shipping, seo)
        // If so, skip validation as those controllers use Request::validate() directly
        $path = $this->path();
        if (str_contains($path, 'settings/email') ||
            str_contains($path, 'settings/payment') ||
            str_contains($path, 'settings/shipping') ||
            str_contains($path, 'settings/seo')) {
            return [];
        }

        $availableThemes = $this->getAvailableThemes();

        return [
            'short_des' => 'required|string',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'active_template' => ['required', 'string', 'in:'.implode(',', $availableThemes)],
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            'google_map_api_key' => 'nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get list of available themes.
     */
    private function getAvailableThemes(): array
    {
        $themesPath = module_path('Front', 'Resources/views/themes');

        if (! is_dir($themesPath)) {
            return ['default'];
        }

        $themes = [];
        $directories = scandir($themesPath);

        foreach ($directories as $dir) {
            if ($dir !== '.' && $dir !== '..' && is_dir($themesPath.'/'.$dir)) {
                $themes[] = $dir;
            }
        }

        // Ensure default theme is always first
        if (in_array('default', $themes, true)) {
            $themes = array_diff($themes, ['default']);
            array_unshift($themes, 'default');
        }

        return array_values($themes);
    }
}
