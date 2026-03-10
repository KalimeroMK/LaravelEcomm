<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Settings;

use Modules\Settings\Actions\UpdateSeoSettingsAction;
use Modules\Settings\Models\Setting;
use Tests\Unit\Actions\ActionTestCase;

class UpdateSeoSettingsActionTest extends ActionTestCase
{
    public function test_execute_updates_seo_settings(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'seo_settings' => [
                'meta_title' => 'Old Title',
                'meta_description' => 'Old description',
                'meta_keywords' => 'old, keywords',
            ],
        ]);

        $action = new UpdateSeoSettingsAction();

        $newData = [
            'meta_title' => 'New Title',
            'meta_description' => 'New description',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertArrayHasKey('meta_title', $result->seo_settings);
        $this->assertArrayHasKey('meta_description', $result->seo_settings);
        $this->assertArrayHasKey('meta_keywords', $result->seo_settings);
        
        // Updated values
        $this->assertEquals('New Title', $result->seo_settings['meta_title']);
        $this->assertEquals('New description', $result->seo_settings['meta_description']);
        
        // Unchanged value should persist
        $this->assertEquals('old, keywords', $result->seo_settings['meta_keywords']);
    }

    public function test_execute_creates_seo_settings_when_null(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'seo_settings' => null,
        ]);

        $action = new UpdateSeoSettingsAction();

        $newData = [
            'meta_title' => 'My Shop',
            'meta_description' => 'Best products online',
            'meta_keywords' => 'shop, products',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('My Shop', $result->seo_settings['meta_title']);
        $this->assertEquals('Best products online', $result->seo_settings['meta_description']);
        $this->assertEquals('shop, products', $result->seo_settings['meta_keywords']);
    }

    public function test_execute_updates_og_tags(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'seo_settings' => [
                'meta_title' => 'Title',
                'og_title' => 'Old OG Title',
                'og_description' => 'Old OG Description',
                'og_type' => 'website',
            ],
        ]);

        $action = new UpdateSeoSettingsAction();

        $newData = [
            'og_title' => 'New OG Title',
            'og_description' => 'New OG Description',
            'og_image' => 'https://example.com/image.jpg',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('Title', $result->seo_settings['meta_title']); // Unchanged
        $this->assertEquals('New OG Title', $result->seo_settings['og_title']);
        $this->assertEquals('New OG Description', $result->seo_settings['og_description']);
        $this->assertEquals('https://example.com/image.jpg', $result->seo_settings['og_image']);
        $this->assertEquals('website', $result->seo_settings['og_type']); // Unchanged
    }

    public function test_execute_updates_twitter_cards(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'seo_settings' => [
                'meta_title' => 'Title',
                'twitter_card' => 'summary',
                'twitter_site' => '@oldhandle',
            ],
        ]);

        $action = new UpdateSeoSettingsAction();

        $newData = [
            'twitter_card' => 'summary_large_image',
            'twitter_site' => '@newhandle',
            'twitter_title' => 'Twitter Title',
            'twitter_description' => 'Twitter Description',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('summary_large_image', $result->seo_settings['twitter_card']);
        $this->assertEquals('@newhandle', $result->seo_settings['twitter_site']);
        $this->assertEquals('Twitter Title', $result->seo_settings['twitter_title']);
        $this->assertEquals('Twitter Description', $result->seo_settings['twitter_description']);
        $this->assertEquals('Title', $result->seo_settings['meta_title']); // Unchanged
    }

    public function test_execute_updates_analytics_ids(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'seo_settings' => [
                'meta_title' => 'Title',
                'google_analytics_id' => null,
                'google_tag_manager_id' => null,
                'facebook_pixel_id' => null,
            ],
        ]);

        $action = new UpdateSeoSettingsAction();

        $newData = [
            'google_analytics_id' => 'GA-12345678',
            'google_tag_manager_id' => 'GTM-123456',
            'facebook_pixel_id' => '1234567890',
        ];

        // Act
        $result = $action->execute($setting, $newData);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('GA-12345678', $result->seo_settings['google_analytics_id']);
        $this->assertEquals('GTM-123456', $result->seo_settings['google_tag_manager_id']);
        $this->assertEquals('1234567890', $result->seo_settings['facebook_pixel_id']);
        $this->assertEquals('Title', $result->seo_settings['meta_title']); // Unchanged
    }

    public function test_execute_returns_fresh_instance(): void
    {
        // Arrange
        $setting = Setting::factory()->create([
            'seo_settings' => ['meta_title' => 'Title'],
        ]);

        $action = new UpdateSeoSettingsAction();

        // Act
        $result = $action->execute($setting, ['meta_description' => 'Description']);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
        ]);
    }

    public function test_execute_preserves_all_existing_seo_settings(): void
    {
        // Arrange
        $originalSettings = [
            'meta_title' => 'Original Title',
            'meta_description' => 'Original Description',
            'meta_keywords' => 'original, keywords',
            'og_title' => 'Original OG Title',
            'og_description' => 'Original OG Description',
            'og_image' => 'original.jpg',
            'og_type' => 'website',
            'og_site_name' => 'Original Site',
            'twitter_card' => 'summary',
            'twitter_site' => '@original',
            'twitter_title' => 'Original Twitter Title',
            'twitter_description' => 'Original Twitter Description',
            'twitter_image' => 'original_twitter.jpg',
        ];
        
        $setting = Setting::factory()->create([
            'seo_settings' => $originalSettings,
        ]);

        $action = new UpdateSeoSettingsAction();

        // Act - update only one field
        $result = $action->execute($setting, ['meta_title' => 'Updated Title']);

        // Assert
        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals('Updated Title', $result->seo_settings['meta_title']);
        $this->assertEquals('Original Description', $result->seo_settings['meta_description']);
        $this->assertEquals('original, keywords', $result->seo_settings['meta_keywords']);
        $this->assertEquals('Original OG Title', $result->seo_settings['og_title']);
        $this->assertEquals('Original OG Description', $result->seo_settings['og_description']);
        $this->assertEquals('original.jpg', $result->seo_settings['og_image']);
        $this->assertEquals('website', $result->seo_settings['og_type']);
        $this->assertEquals('Original Site', $result->seo_settings['og_site_name']);
        $this->assertEquals('summary', $result->seo_settings['twitter_card']);
        $this->assertEquals('@original', $result->seo_settings['twitter_site']);
        $this->assertEquals('Original Twitter Title', $result->seo_settings['twitter_title']);
        $this->assertEquals('Original Twitter Description', $result->seo_settings['twitter_description']);
        $this->assertEquals('original_twitter.jpg', $result->seo_settings['twitter_image']);
    }
}
