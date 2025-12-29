<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Settings\Models\Setting;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use RefreshDatabase;
    use WithFaker;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        // Create and assign settings permissions
        $permissions = [
            'setting-list',
            'setting-update',
        ];

        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }

        $this->user->assignRole($adminRole);

        $this->token = $this->user->createToken('test-token')->plainTextToken;

        // Create a setting if it doesn't exist
        Setting::firstOrCreate([], [
            'description' => 'Test Description',
            'short_des' => 'Test Short',
            'logo' => 'test.jpg',
            'address' => 'Test Address',
            'phone' => '1234567890',
            'email' => 'test@test.com',
            'site-name' => 'Test Store',
        ]);
    }

    #[Test]
    public function test_get_all_settings(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('GET', '/api/v1/settings');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'description',
                'short_des',
                'logo',
                'address',
                'phone',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    #[Test]
    public function test_update_settings(): void
    {
        $setting = Setting::first();
        $data = [
            'description' => 'Updated Description',
            'short_des' => 'Updated Short',
            'email' => 'updated@teststore.com',
            'address' => $setting->address ?? 'Test Address',
            'phone' => $setting->phone ?? '1234567890',
            'active_template' => 'default',
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('PUT', "/api/v1/settings/{$setting->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        $setting->refresh();
        expect($setting->description)->toBe('Updated Description');
    }

    #[Test]
    public function test_get_email_settings(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('GET', '/api/v1/settings/email');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'settings',
                'email_settings',
            ],
        ]);
    }

    #[Test]
    public function test_update_email_settings(): void
    {
        $data = [
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.example.com',
            'mail_port' => 587,
            'mail_username' => 'test@example.com',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'Test Store',
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('PUT', '/api/v1/settings/email', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        $setting = Setting::first();
        expect($setting->email_settings)->toBeArray();
        expect($setting->email_settings['mail_driver'])->toBe('smtp');
    }

    #[Test]
    public function test_get_payment_settings(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('GET', '/api/v1/settings/payment');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'settings',
                'payment_settings',
            ],
        ]);
    }

    #[Test]
    public function test_update_payment_settings(): void
    {
        $data = [
            'stripe_enabled' => true,
            'stripe_public_key' => 'pk_test_123',
            'stripe_secret_key' => 'sk_test_123',
            'paypal_enabled' => true,
            'cod_enabled' => true,
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('PUT', '/api/v1/settings/payment', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        $setting = Setting::first();
        expect($setting->payment_settings)->toBeArray();
        expect($setting->payment_settings['stripe_enabled'])->toBeTrue();
    }

    #[Test]
    public function test_get_shipping_settings(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('GET', '/api/v1/settings/shipping');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'settings',
                'shipping_settings',
            ],
        ]);
    }

    #[Test]
    public function test_update_shipping_settings(): void
    {
        $data = [
            'default_shipping_method' => 'flat_rate',
            'flat_rate_shipping' => 10.00,
            'free_shipping_threshold' => 100.00,
            'estimated_delivery_days' => 5,
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('PUT', '/api/v1/settings/shipping', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        $setting = Setting::first();
        expect($setting->shipping_settings)->toBeArray();
        expect($setting->shipping_settings['default_shipping_method'])->toBe('flat_rate');
    }

    #[Test]
    public function test_get_seo_settings(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('GET', '/api/v1/settings/seo');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'settings',
                'seo_settings',
            ],
        ]);
    }

    #[Test]
    public function test_update_seo_settings(): void
    {
        $data = [
            'meta_title' => 'Test Store - SEO Title',
            'meta_description' => 'Test Store Description',
            'meta_keywords' => 'test, store, ecommerce',
            'google_analytics_id' => 'UA-123456789-1',
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('PUT', '/api/v1/settings/seo', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        $setting = Setting::first();
        expect($setting->seo_settings)->toBeArray();
        expect($setting->seo_settings['meta_title'])->toBe('Test Store - SEO Title');
    }
}
