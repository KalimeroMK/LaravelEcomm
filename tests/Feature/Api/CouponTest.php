<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\Coupon\Models\Coupon;
use Tests\Feature\CoreTest;

class CouponTest extends CoreTest
{
    public string $url = '/api/v1/coupon/';
    public $model = Coupon::class;
    
    use WithFaker;
    
    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_coupon(): TestResponse
    {
        $token = $this->authenticate();
        
        $data = [
            'code'   => $this->faker->word,
            'value'  => $this->faker->randomFloat(),
            'type'   => 'fixed',
            'status' => 'active',
        ];
        
        return $this->create($this->url, $token, $data);
    }
    
    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_coupon(): TestResponse
    {
        $token = $this->authenticate();
        $data  = [
            'code'   => $this->faker->word,
            'value'  => $this->faker->randomFloat(),
            'type'   => 'percent',
            'status' => 'active',
        ];
        
        $id = $this->model::firstOrFail()->id;
        
        return $this->updatePUT($this->url, $token, $data, $id);
    }
    
    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_coupon(): TestResponse
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
    public function test_get_all_coupon(): TestResponse
    {
        $token = $this->authenticate();
        
        return $this->list($this->url, $token);
    }
    
    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_coupon(): TestResponse
    {
        $token = $this->authenticate();
        $id    = $this->model::firstOrFail()->id;
        
        return $this->destroy($this->url, $token, $id);
    }
}
