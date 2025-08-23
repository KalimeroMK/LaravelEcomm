<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Brand\Models\Brand;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;

    public string $url = '/api/v1/brands';

    /**
     * test create product.
     */
    #[Test]
    public function test_create_brand(): TestResponse
    {
        $data = [
            'title' => 'Test Brand '.time(),
            'status' => 'active',
            'images' => [\Illuminate\Http\UploadedFile::fake()->image('brand.jpg')],
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    #[Test]
    public function test_update_brand(): TestResponse
    {
        $brand = Brand::factory()->create();
        $data = [
            'title' => time().'Test title1',
            'status' => 'inactive',
            'images' => [\Illuminate\Http\UploadedFile::fake()->image('brand_update.jpg')],
        ];

        $id = $brand->id;

        return $this->update($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_brand(): TestResponse
    {
        $brand = Brand::factory()->create();
        $id = $brand->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_brand(): TestResponse
    {
        Brand::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_brand(): TestResponse
    {
        $brand = Brand::factory()->create();
        $id = $brand->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        Brand::factory()->count(2)->create();
        $response = $this->json('GET', '/api/v1/brands');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'title',
                        'slug',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],

            ]
        );
    }
}
