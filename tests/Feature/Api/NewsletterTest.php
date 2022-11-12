<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;
    
    public string $url = '/api/v1/newsletter/';
    
    use WithFaker;
    
    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_newsletter(): TestResponse
    {
        $data = [
            'email' => $this->faker->unique()->safeEmail,
        ];
        
        return $this->create($this->url, $data);
    }
    
    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_newsletter(): TestResponse
    {
        $data = [
            'email' => $this->faker->unique()->safeEmail,
        ];
        
        $id = Newsletter::firstOrFail()->id;
        
        return $this->updatePUT($this->url, $data, $id);
    }
    
    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_newsletter(): TestResponse
    {
        $id = Newsletter::firstOrFail()->id;
        
        return $this->show($this->url, $id);
    }
    
    /**
     * test get all products.
     *
     * @return TestResponse
     */
    public function test_get_all_newsletter(): TestResponse
    {
        return $this->list($this->url);
    }
    
    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_newsletter(): TestResponse
    {
        $id = Newsletter::firstOrFail()->id;
        
        return $this->destroy($this->url, $id);
    }
    
    public function test_delete_message(): TestResponse
    {
        $id = Message::firstOrFail()->id;
        
        return $this->destroy($this->url, $id);
    }
    
    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/newsletter/');
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
