<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Database\Seeders\PermissionTableSeeder;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The /admin web surface inherits only `auth` from the module route service
 * providers. Controllers that also call authorize() are covered; these ones did
 * not, so any authenticated customer could reach them:
 *
 *   AdminController, AnalyticsController, EmailTemplateController,
 *   EmailCampaignController, NewsletterAnalyticsController,
 *   ProductStatsController, ProductImportExportController
 *
 * Modules/Admin/Routes/web.php even carried the comment
 * "temporarily without auth for testing" above the analytics group.
 */
class AdminWebAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionTableSeeder::class);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function adminOnlyPages(): array
    {
        return [
            'dashboard' => ['/admin'],
            'unread messages' => ['/admin/messages/five'],
            'analytics dashboard' => ['/admin/analytics'],
            'analytics sales' => ['/admin/analytics/sales'],
            'abandoned carts' => ['/admin/analytics/abandoned-carts'],
            'email templates' => ['/admin/email-templates'],
            'email campaigns' => ['/admin/email-campaigns'],
            'newsletter analytics' => ['/admin/newsletters/analytics'],
            'product stats' => ['/admin/product-stats'],
            'product export' => ['/admin/products-export'],
            'product import form' => ['/admin/products-import'],
        ];
    }

    #[Test]
    #[DataProvider('adminOnlyPages')]
    public function test_a_customer_cannot_reach_back_office_pages(string $uri): void
    {
        $customer = User::factory()->create();
        $customer->syncRoles(['client']);

        $response = $this->actingAs($customer)->get($uri);

        $this->assertContains(
            $response->getStatusCode(),
            [403, 404],
            sprintf('%s returned %d - a customer must not reach it.', $uri, $response->getStatusCode())
        );
    }

    #[Test]
    #[DataProvider('adminOnlyPages')]
    public function test_a_guest_cannot_reach_back_office_pages(string $uri): void
    {
        $response = $this->get($uri);

        $this->assertContains(
            $response->getStatusCode(),
            [302, 401, 403],
            sprintf('%s returned %d for a guest.', $uri, $response->getStatusCode())
        );
    }

    #[Test]
    public function test_an_admin_can_still_reach_the_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)->get('/admin')->assertStatus(200);
    }
}
