<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Product\Models\Product;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;
    
    public string $url = '/api/v1/product/';
    
    use WithFaker;
    
    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_product(): TestResponse
    {
        Storage::fake('uploads');
        
        $data = [
            'title'        => $this->faker->unique(true)->word,
            'color'        => $this->faker->unique(true)->word,
            'summary'      => $this->faker->text,
            'description'  => $this->faker->text,
            'condition_id' => $this->faker->numberBetween(1, 2),
            'photo'        => UploadedFile::fake()->image('file.png', 600, 600),
            'stock'        => 100,
            'price'        => $this->faker->numberBetween(1, 9999),
            'discount'     => 10,
            'is_featured'  => true,
            'status'       => 'active',
            'brand_id'     => $this->faker->numberBetween(1, 10),
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ];
        
        return $this->create($this->url, $data);
    }
    
    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_product(): TestResponse
    {
        $data = [
            'title'        => $this->faker->unique(true)->word,
            'summary'      => $this->faker->text,
            'description'  => $this->faker->text,
            'color'        => $this->faker->unique(true)->word,
            'size'         => 1,
            'condition_id' => $this->faker->numberBetween(1, 2),
            'photo'        => UploadedFile::fake()->image('file.png', 600, 600),
            'stock'        => 100,
            'price'        => $this->faker->numberBetween(1, 9999),
            'discount'     => 10,
            'is_featured'  => true,
            'status'       => 'active',
            'brand_id'     => $this->faker->numberBetween(1, 10),
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ];
        
        $id = Product::firstOrFail()->id;
        
        return $this->update($this->url, $data, $id);
    }
    
    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_product(): TestResponse
    {
        $id = Product::firstOrFail()->id;
        
        return $this->show($this->url, $id);
    }
    
    /**
     * test get all products.
     *
     * @return TestResponse
     */
    public function test_get_all_product(): TestResponse
    {
        return $this->list($this->url);
    }
    
    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_product(): TestResponse
    {
        $id = Product::firstOrFail()->id;
        
        return $this->destroy($this->url, $id);
    }
    
    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/product/');
        $response->assertStatus(200);
        
        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'title',
                        'slug',
                        'summary',
                        'description',
                        'photo',
                        'stock',
                        'size',
                        'condition',
                        'status',
                        'price',
                        'discount',
                        'is_featured',
                        'created_at',
                        'updated_at',
                        'carts_count',
                        'product_reviews_count',
                        'wishlists_count',
                        'categories_count',
                        'image_url',
                        'color',
                        'd_deal',
                        'get_review_count',
                        'sizes_count',
                        'brand_id',
                        'condition_id',
                    ],
                ],
            
            ]
        );
    }
}
