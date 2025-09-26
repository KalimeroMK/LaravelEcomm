<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\Page\Models\Page;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class PageTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public string $url = '/api/v1/pages';
    
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create and assign page permissions
        $permissions = [
            'page-list',
            'page-create', 
            'page-update',
            'page-delete'
        ];
        
        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }
        
        $this->user->assignRole($adminRole);
        
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * test create page.
     */
    #[Test]
    public function test_create_page(): TestResponse
    {
        $data = [
            'title' => 'Test Page ' . time(),
            'slug' => 'test-page-' . time(),
            'content' => 'This is test page content',
            'is_active' => true,
            'user_id' => $this->user->id,
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test find page.
     */
    #[Test]
    public function test_find_page(): TestResponse
    {
        $page = Page::factory()->create(['user_id' => $this->user->id]);

        return $this->show($this->url, $page->id);
    }

    /**
     * test get all pages.
     */
    #[Test]
    public function test_get_all_pages(): TestResponse
    {
        Page::factory()->create(['user_id' => $this->user->id]);
        Page::factory()->create(['user_id' => $this->user->id]);
        Page::factory()->create(['user_id' => $this->user->id]);

        return $this->list($this->url);
    }

    /**
     * test delete page.
     */
    #[Test]
    public function test_delete_page(): TestResponse
    {
        $page = Page::factory()->create(['user_id' => $this->user->id]);

        return $this->destroy($this->url, $page->id);
    }

    #[Test]
    public function test_structure(): void
    {
        Page::factory()->create(['user_id' => $this->user->id]);
        Page::factory()->create(['user_id' => $this->user->id]);
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/pages');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'content',
                    'is_active',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
