<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Modules\Product\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/products';

    /**
     * test create product.
     */
    #[Test]
    public function test_create_product(): TestResponse
    {
        $data = Product::factory()->make()->toArray();

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    #[Test]
    public function test_update_product(): TestResponse
    {
        $id = Product::factory()->create()->id;
        $data = [
            'title' => $this->faker->word,
            'sku' => 'SKU-'.mb_strtoupper(Str::random(10)),
        ];

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_product(): TestResponse
    {
        $id = Product::factory()->create()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_product(): TestResponse
    {
        Product::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_product(): TestResponse
    {
        $product = Product::factory()->create();
        $id = $product->id;

        return $this->destroy($this->url, $id);
    }
}
