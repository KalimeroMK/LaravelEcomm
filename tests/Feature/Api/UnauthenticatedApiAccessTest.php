<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Guards the back-office API surface against anonymous access.
 *
 * The Newsletter module and the user-behaviour analytics group shipped with no
 * middleware at all. Most severely, POST newsletter/campaigns/send-all was an
 * open relay: anyone could trigger a mail-out to the entire subscriber list.
 */
class UnauthenticatedApiAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, string}>
     */
    public static function backOfficeEndpoints(): array
    {
        return [
            'mass mail-out' => ['POST', '/api/v1/newsletter/campaigns/send-all'],
            'segment mail-out' => ['POST', '/api/v1/newsletter/campaigns/send-segment'],
            'subscriber analytics' => ['GET', '/api/v1/newsletter/analytics/subscribers'],
            'subscriber list' => ['GET', '/api/v1/newsletters'],
            'email template list' => ['GET', '/api/v1/email-templates'],
            'email template create' => ['POST', '/api/v1/email-templates'],
            'visitor sessions' => ['GET', '/api/v1/admin/analytics/sessions'],
            'visitor geography' => ['GET', '/api/v1/admin/analytics/geographic'],
            'sales analytics' => ['GET', '/api/v1/admin/analytics/sales'],
        ];
    }

    #[Test]
    #[DataProvider('backOfficeEndpoints')]
    public function test_back_office_endpoints_reject_anonymous_callers(string $method, string $uri): void
    {
        $response = $this->json($method, $uri);

        $this->assertSame(
            401,
            $response->getStatusCode(),
            sprintf('%s %s must require authentication.', $method, $uri)
        );
    }

    /**
     * Routes that carry no auth middleware but whose controllers call
     * $this->authorize(). They must still fail closed for anonymous callers.
     *
     * @return array<string, array{string, string}>
     */
    public static function destructiveEndpointsWithoutRouteMiddleware(): array
    {
        return [
            'delete a product' => ['DELETE', '/api/v1/products/1'],
            'delete an order' => ['DELETE', '/api/v1/orders/1'],
            'delete a role' => ['DELETE', '/api/v1/roles/1'],
            'delete a permission' => ['DELETE', '/api/v1/permissions/1'],
            'delete a page' => ['DELETE', '/api/v1/pages/1'],
            'list orders' => ['GET', '/api/v1/orders'],
            'list permissions' => ['GET', '/api/v1/permissions'],
        ];
    }

    #[Test]
    #[DataProvider('destructiveEndpointsWithoutRouteMiddleware')]
    public function test_controller_level_authorization_fails_closed_for_anonymous_callers(string $method, string $uri): void
    {
        $response = $this->json($method, $uri);

        $this->assertContains(
            $response->getStatusCode(),
            [401, 403, 404],
            sprintf('%s %s returned %d - it must not be reachable anonymously.', $method, $uri, $response->getStatusCode())
        );
    }

    #[Test]
    #[DataProvider('backOfficeEndpoints')]
    public function test_back_office_endpoints_reject_ordinary_customers(string $method, string $uri): void
    {
        Role::firstOrCreate(['name' => 'client']);
        $customer = User::factory()->create();
        $customer->syncRoles(['client']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$customer->createToken('customer')->plainTextToken,
        ])->json($method, $uri);

        $this->assertSame(
            403,
            $response->getStatusCode(),
            sprintf('%s %s must be denied to a plain customer account.', $method, $uri)
        );
    }
}
