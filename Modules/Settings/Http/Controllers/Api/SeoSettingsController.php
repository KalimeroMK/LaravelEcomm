<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Settings\Actions\FindSettingAction;
use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Actions\UpdateSeoSettingsAction;
use Modules\Settings\Models\Setting;

class SeoSettingsController extends CoreController
{
    public function __construct(
        private readonly GetSettingsAction $getSettingsAction,
        private readonly FindSettingAction $findSettingAction,
        private readonly UpdateSeoSettingsAction $updateSeoSettingsAction
    ) {}

    /**
     * Get SEO settings
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Setting::class);
        $setting = $this->findSettingAction->execute();

        if (! $setting) {
            return $this
                ->setCode(404)
                ->setMessage('Settings not found.')
                ->respond(null);
        }

        return $this
            ->setMessage('SEO settings retrieved successfully.')
            ->respond([
                'settings' => $setting,
                'seo_settings' => $setting->seo_settings ?? [],
            ]);
    }

    /**
     * Update SEO settings
     */
    public function update(Request $request): JsonResponse
    {
        $setting = $this->findSettingAction->execute();

        if (! $setting) {
            return $this
                ->setCode(404)
                ->setMessage('Settings not found.')
                ->respond(null);
        }

        $this->authorize('update', $setting);

        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:255',
            'twitter_card' => 'nullable|in:summary,summary_large_image',
            'twitter_site' => 'nullable|string|max:255',
            'google_analytics_id' => 'nullable|string|max:255',
            'google_tag_manager_id' => 'nullable|string|max:255',
            'facebook_pixel_id' => 'nullable|string|max:255',
            'robots_txt' => 'nullable|string',
            'sitemap_enabled' => 'boolean',
        ]);

        $updatedSetting = $this->updateSeoSettingsAction->execute($setting, $validated);

        return $this
            ->setMessage('SEO settings updated successfully.')
            ->respond([
                'settings' => $updatedSetting,
                'seo_settings' => $updatedSetting->seo_settings,
            ]);
    }
}
