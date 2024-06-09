<?php

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

    public string $url = '/api/v1/brand/';

    use BaseTestTrait;
    use WithoutMiddleware;

    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_brand(): TestResponse
    {
        Storage::fake('uploads');

        $data = [
            'title' => time() . 'Test title',
            'images' => [UploadedFile::fake()->image('updated_file.png', 600, 600)],
            'status' => 'inactive',
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_brand(): TestResponse
    {
        $data = [
            'title' => time() . 'Test title1',
            'description' => time() . 'test-description',
            'status' => 'inactive',
        ];

        $id = Brand::firstOrFail()->id;

        return $this->update($this->url, $data, $id);
    }

    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_brand(): TestResponse
    {
        $id = Brand::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     *
     * @return TestResponse
     */
    public function test_get_all_brand(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     *
     * @return TestResponse
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
