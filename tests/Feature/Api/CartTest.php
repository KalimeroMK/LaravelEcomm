<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\TestResponse;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Tests\Feature\CoreTest;

class CartTest extends CoreTest
{
    public string $url = '/api/v1/cart/';
    public $model = Cart::class;
    
    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_cart(): TestResponse
    {
        $token   = $this->authenticate();
        $product = Product::firstOrFail();
        
        $data = [
            'slug'       => $product->slug,
            'user_id'    => Auth::id(),
            'product_id' => $product->id,
            'price'      => ($product->price - ($product->price * $product->discount) / 100),
            'amount'     => ($product->price - ($product->price * $product->discount) / 100) * 14,
            'quantity'   => 5,
        ];
        
        return $this->create($this->url, $token, $data);
    }
    
    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_cart(): TestResponse
    {
        $token = $this->authenticate();
        $data  = [
            'slug'     => Product::firstOrFail()->slug,
            'quantity' => 5,
        ];
        
        $id = $this->model::firstOrFail()->id;
        
        return $this->updatePUT($this->url, $token, $data, $id);
    }
    
    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_cart(): TestResponse
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
    public function test_get_all_cart(): TestResponse
    {
        $token = $this->authenticate();
        
        return $this->list($this->url, $token);
    }
    
    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_cart(): TestResponse
    {
        $token = $this->authenticate();
        $id    = $this->model::firstOrFail()->id;
        
        return $this->destroy($this->url, $token, $id);
    }
}
