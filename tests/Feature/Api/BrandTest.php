<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Brand\Models\Brand;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;

    public string $url = '/api/v1/brand/';

    /**
     * test create product.
     */
    public function test_create_brand(): TestResponse
    {
        Storage::fake('uploads');

        $data = [
            'title' => time().'Test title',
            'images' => [UploadedFile::fake()->image('updated_file.png', 600, 600)],
            'status' => 'inactive',
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    public function test_update_brand(): TestResponse
    {
        $data = [
            'title' => time().'Test title1',
            'description' => time().'test-description',
            'status' => 'inactive',
        ];

        $id = Brand::firstOrFail()->id;

        return $this->update($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    public function test_find_brand(): TestResponse
    {
        $id = Brand::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    public function test_get_all_brand(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    public function test_delete_brand(): TestResponse
    {
        $id = Brand::firstOrFail()->id;

        return $this->destroy($this->url, $id);
    }

    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/brand/');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'title',
                        'slug',
                        'images',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],

            ]
        );
    }
}
