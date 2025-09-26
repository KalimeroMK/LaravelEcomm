<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\Complaint\Models\Complaint;
use Modules\Order\Models\Order;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class ComplaintTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public string $url = '/api/v1/complaints';
    
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create and assign complaint permissions
        $permissions = [
            'complaint-list',
            'complaint-create', 
            'complaint-edit',
            'complaint-delete'
        ];
        
        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }
        
        $this->user->assignRole($adminRole);
        
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * test create complaint.
     */
    #[Test]
    public function test_create_complaint(): TestResponse
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        
        $data = [
            'order_id' => $order->id,
            'subject' => 'Test Complaint ' . time(),
            'description' => 'This is a test complaint description',
            'status' => 'pending',
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test find complaint.
     */
    #[Test]
    public function test_find_complaint(): TestResponse
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        $complaint = Complaint::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
        ]);

        return $this->show($this->url, $complaint->id);
    }

    /**
     * test get all complaints.
     */
    #[Test]
    public function test_get_all_complaints(): TestResponse
    {
        $order1 = Order::factory()->create(['user_id' => $this->user->id]);
        $order2 = Order::factory()->create(['user_id' => $this->user->id]);
        $order3 = Order::factory()->create(['user_id' => $this->user->id]);
        
        Complaint::factory()->create([
            'order_id' => $order1->id,
            'user_id' => $this->user->id,
        ]);
        Complaint::factory()->create([
            'order_id' => $order2->id,
            'user_id' => $this->user->id,
        ]);
        Complaint::factory()->create([
            'order_id' => $order3->id,
            'user_id' => $this->user->id,
        ]);

        return $this->list($this->url);
    }

    /**
     * test create complaint with order.
     */
    #[Test]
    public function test_create_complaint_with_order(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', "/api/v1/complaints/create/{$order->id}");
        
        $response->assertStatus(200);
    }

    #[Test]
    public function test_structure(): void
    {
        $order1 = Order::factory()->create(['user_id' => $this->user->id]);
        $order2 = Order::factory()->create(['user_id' => $this->user->id]);
        
        Complaint::factory()->create([
            'order_id' => $order1->id,
            'user_id' => $this->user->id,
        ]);
        Complaint::factory()->create([
            'order_id' => $order2->id,
            'user_id' => $this->user->id,
        ]);
        
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/complaints');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'order_id',
                    'user_id',
                    'description',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
