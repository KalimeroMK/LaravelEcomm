<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Laravel 11 stopped applying `throttle:api` to the api middleware group by
 * default, and nothing here replaced the RouteServiceProvider that used to
 * define the limiters. The api group contained only SubstituteBindings, so
 * /api/v1/login accepted unlimited password guesses and /api/v1/register
 * accepted unlimited account creation.
 */
class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'client']);

        // Limiter counters live in the cache and would otherwise leak between
        // tests in this class.
        Cache::flush();
    }

    #[Test]
    public function test_repeated_failed_logins_are_throttled(): void
    {
        $user = User::factory()->create();

        $statuses = [];

        for ($attempt = 0; $attempt < 8; $attempt++) {
            $statuses[] = $this->postJson('/api/v1/login', [
                'email' => $user->email,
                'password' => 'wrong-password-'.$attempt,
            ])->getStatusCode();
        }

        $this->assertContains(
            429,
            $statuses,
            'Brute forcing /api/v1/login was never rate limited. Statuses: '.implode(',', $statuses)
        );
    }

    #[Test]
    public function test_rotating_the_email_does_not_sidestep_the_login_limit(): void
    {
        $statuses = [];

        for ($attempt = 0; $attempt < 8; $attempt++) {
            $statuses[] = $this->postJson('/api/v1/login', [
                'email' => "victim{$attempt}@example.com",
                'password' => 'guess',
            ])->getStatusCode();
        }

        $this->assertContains(
            429,
            $statuses,
            'The limiter must key on IP, not on the submitted email.'
        );
    }

    #[Test]
    public function test_bulk_registration_is_throttled(): void
    {
        $statuses = [];

        for ($attempt = 0; $attempt < 8; $attempt++) {
            $statuses[] = $this->postJson('/api/v1/register', [
                'name' => "Spam {$attempt}",
                'email' => "spam{$attempt}@example.com",
                'password' => 'Str0ng-Passphrase-'.$attempt,
                'password_confirmation' => 'Str0ng-Passphrase-'.$attempt,
            ])->getStatusCode();
        }

        $this->assertContains(
            429,
            $statuses,
            'Unlimited account creation. Statuses: '.implode(',', $statuses)
        );
    }

    #[Test]
    public function test_the_general_api_group_carries_a_limiter(): void
    {
        // gatherMiddleware() reports the group name, not its contents, so the
        // group itself has to be resolved.
        $apiGroup = Route::getMiddlewareGroups()['api'] ?? [];

        $this->assertContains(
            'throttle:api',
            $apiGroup,
            'The api middleware group must apply throttle:api.'
        );

        $route = collect(Route::getRoutes()->getRoutes())
            ->first(fn ($r) => $r->uri() === 'api/v1/users' && in_array('GET', $r->methods(), true));

        $this->assertNotNull($route, 'Expected api/v1/users to be registered.');
        $this->assertContains('api', $route->gatherMiddleware());
    }

    #[Test]
    public function test_a_normal_login_still_succeeds(): void
    {
        // The limit must not break ordinary use.
        // NB: assign the plain password - UserObserver::updating() hashes it.
        // Passing an already-hashed value here would double-hash it.
        $user = User::factory()->create();
        $user->forceFill(['password' => 'correct-horse'])->save();

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'correct-horse',
        ])->assertStatus(200);
    }
}
