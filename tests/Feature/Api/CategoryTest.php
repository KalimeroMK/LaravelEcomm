<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\Category\Models\Category;
use Tests\Feature\CoreTest;

class CategoryTest extends CoreTest
{
    public string $url = '/api/v1/category/';
    use WithFaker;
    
    public $model = Category::class;
    
    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_category(): TestResponse
    {
        $token = $this->authenticate();
        
        $data = [
            'title'     => $this->faker->word,
            'status'    => 'active',
            'parent_id' => null,
            '_lft'      => $this->faker->randomNumber(),
            '_rgt'      => $this->faker->randomNumber(),
        ];
        
        return $this->create($this->url, $token, $data);
    }
    
    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_category(): TestResponse
    {
        $token = $this->authenticate();
        $data  = [
            'parent_id' => 5,
        ];
        
        $id = $this->model::firstOrFail()->id;
        
        return $this->updatePUT($this->url, $token, $data, $id);
    }
    
    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_category(): TestResponse
    {
        $token = $this->authenticate();
        $id    = $this->model::firstOrFail()->id;
        
        return $this->show($this->url, $token, $id);
    }
    
    /**
     * test get all products.
     *
     * @return TestResponse
     */
    public function test_get_all_category(): TestResponse
    {
        $token = $this->authenticate();
        
        return $this->list($this->url, $token);
    }
    
    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_category(): TestResponse
    {
        $token = $this->authenticate();
        $id    = $this->model::firstOrFail()->id;
        
        return $this->destroy($this->url, $token, $id);
    }
}
