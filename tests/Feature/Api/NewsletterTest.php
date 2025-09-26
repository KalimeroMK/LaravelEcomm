<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public string $url = '/api/v1/newsletters';
    
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create and assign newsletter permissions
        $permissions = [
            'newsletter-list',
            'newsletter-create', 
            'newsletter-update',
            'newsletter-delete',
            'newsletter-show'
        ];
        
        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }
        
        $this->user->assignRole($adminRole);
        
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * test create newsletter.
     */
    #[Test]
    public function test_create_newsletter(): TestResponse
    {
        $data = [
            'email' => $this->faker->unique()->safeEmail,
            'status' => 'active',
            'is_validated' => true,
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_newsletter(): TestResponse
    {
        Newsletter::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_newsletter(): TestResponse
    {
        $newsletter = Newsletter::factory()->create();
        $id = $newsletter->id;

        return $this->destroy($this->url, $id);
    }

    public function test_delete_message(): TestResponse
    {
        $message = Message::factory()->create();
        $id = $message->id;

        return $this->destroy('/api/v1/messages', $id);
    }

    #[Test]
    public function test_structure()
    {
        Newsletter::factory()->count(2)->create();
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/newsletters');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'email',
                        'token',
                        'is_validated',
                        'created_at',
                        'updated_at',
                    ],
                ],

            ]
        );
    }
}
