<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminWebRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the 'client' role exists for the 'web' guard
        Role::firstOrCreate([
            'name' => 'client',
            'guard_name' => 'web',
        ]);
    }

    #[Test]
    public function admin_dashboard_index_route_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('admin::index');
        $response->assertViewHasAll(['paidOrdersByMonth', 'data']);
    }

    #[Test]
    public function admin_messages_five_returns_json_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/messages/five');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }
}
