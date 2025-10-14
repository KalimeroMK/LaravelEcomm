<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminTest extends TestCase
{
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

        // Create and assign admin permissions
        $permissions = [
            'admin-analytics-dashboard',
            'admin-analytics-overview',
            'admin-analytics-sales',
            'admin-analytics-users',
            'admin-analytics-products',
            'admin-analytics-content',
            'admin-analytics-marketing',
            'admin-analytics-performance',
            'admin-analytics-real-time',
            'admin-analytics-date-range',
            'admin-analytics-export',
        ];

        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }

        $this->user->assignRole($adminRole);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * test admin analytics dashboard.
     */
    #[Test]
    public function test_admin_analytics_dashboard(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/dashboard');

        $response->assertStatus(200);
    }

    /**
     * test admin analytics overview.
     */
    #[Test]
    public function test_admin_analytics_overview(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/overview');

        $response->assertStatus(200);
    }

    /**
     * test admin analytics sales.
     */
    #[Test]
    public function test_admin_analytics_sales(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/sales');

        $response->assertStatus(200);
    }

    /**
     * test admin analytics users.
     */
    #[Test]
    public function test_admin_analytics_users(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/users');

        $response->assertStatus(200);
    }

    /**
     * test admin analytics products.
     */
    #[Test]
    public function test_admin_analytics_products(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/products');

        $response->assertStatus(200);
    }

    /**
     * test admin analytics content.
     */
    #[Test]
    public function test_admin_analytics_content(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/content');

        $response->assertStatus(200);
    }

    /**
     * test admin analytics marketing.
     */
    #[Test]
    public function test_admin_analytics_marketing(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/marketing');

        $response->assertStatus(200);
    }

    /**
     * test admin analytics performance.
     */
    #[Test]
    public function test_admin_analytics_performance(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/performance');

        $response->assertStatus(200);
    }

    /**
     * test admin analytics real-time.
     */
    #[Test]
    public function test_admin_analytics_real_time(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/real-time');

        $response->assertStatus(200);
    }

    /**
     * test admin analytics date-range.
     */
    #[Test]
    public function test_admin_analytics_date_range(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/admin/analytics/date-range', [
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'type' => 'overview',
        ]);

        $response->assertStatus(200);
    }

    /**
     * test admin analytics export.
     */
    #[Test]
    public function test_admin_analytics_export(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('POST', '/api/v1/admin/analytics/export', [
            'type' => 'overview',
            'format' => 'json',
        ]);

        $response->assertStatus(200);
    }
}
