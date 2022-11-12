<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\TestResponse;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class CartTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;
    
    public string $url = '/api/v1/cart/';
    
    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_cart(): TestResponse
    {
        $product = Product::firstOrFail();
        
        $data = [
            'slug'       => $product->slug,
            'user_id'    => Auth::id(),
            'product_id' => $product->id,
            'price'      => ($product->price - ($product->price * $product->discount) / 100),
            'amount'     => ($product->price - ($product->price * $product->discount) / 100) * 14,
            'quantity'   => 5,
        ];
        
        return $this->create($this->url, $data);
    }
    
    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_cart(): TestResponse
    {
        $data = [
            'slug'     => Product::firstOrFail()->slug,
            'quantity' => 5,
        ];
        
        $id = Cart::firstOrFail()->id;
        
        return $this->updatePUT($this->url, $data, $id);
    }
    
    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_cart(): TestResponse
    {
        $id = Cart::firstOrFail()->id;
        
        return $this->show($this->url, $id);
    }
    
    /**
     * test get all products.
     *
     * @return TestResponse
     */
    public function test_get_all_cart(): TestResponse
    {
        return $this->list($this->url);
    }
    
    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_cart(): TestResponse
    {
        $id = Cart::firstOrFail()->id;
        
        return $this->destroy($this->url, $id);
    }
    
    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/cart/');
        $response->assertStatus(200);
        
        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'price',
                        'status',
                        'quantity',
                        'amount',
                        'created_at',
                        'updated_at',
                        'wishlists_count',
                        'product_id',
                        'order_id',
                        'user_id',
                    ],
                ],
            
            ]
        );
    }
}
