<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Message\Models\Message;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;
    
    public string $url = '/api/v1/message/';
    
    use WithFaker;
    
    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_message(): TestResponse
    {
        $data = [
            'name'    => $this->faker->name,
            'subject' => $this->faker->word,
            'email'   => $this->faker->unique()->safeEmail,
            'photo'   => $this->faker->word,
            'phone'   => $this->faker->phoneNumber,
            'message' => $this->faker->sentence,
            'read_at' => $this->faker->time,
        ];
        
        return $this->create($this->url, $data);
    }
    
    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_message(): TestResponse
    {
        $data = [
            'name'    => $this->faker->name,
            'subject' => $this->faker->word,
            'email'   => $this->faker->unique()->safeEmail,
            'phone'   => $this->faker->phoneNumber,
            'message' => $this->faker->sentence,
            'read_at' => $this->faker->time,
        ];
        
        $id = Message::firstOrFail()->id;
        
        return $this->updatePUT($this->url, $data, $id);
    }
    
    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_message(): TestResponse
    {
        $id = Message::firstOrFail()->id;
        
        return $this->show($this->url, $id);
    }
    
    /**
     * test get all products.
     *
     * @return TestResponse
     */
    public function test_get_all_message(): TestResponse
    {
        return $this->list($this->url);
    }
    
    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_message(): TestResponse
    {
        $id = Message::firstOrFail()->id;
        
        return $this->destroy($this->url, $id);
    }
    
    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/message/');
        $response->assertStatus(200);
        
        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'subject',
                        'email',
                        'photo',
                        'phone',
                        'message',
                        'read_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
            
            ]
        );
    }
}
