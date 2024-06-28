<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Banner\Models\Banner;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use BaseTestTrait;

    public string $url = '/api/v1/banners/';
    use WithFaker;
    use WithoutMiddleware;

    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_banner(): TestResponse
    {
        Storage::fake('uploads');

        $data = [
            'title' => $this->faker->unique()->word,
            'description' => $this->faker->text,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
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
    public function test_update_banner(): TestResponse
    {
        $data = [
            'title' => time() . 'Test title',
            'description' => time() . 'test-description',
            'status' => 'inactive',
            'images' => [UploadedFile::fake()->image('updated_file.png', 600, 600)],

        ];

        $id = Banner::firstOrFail()->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_banner(): TestResponse
    {
        $id = Banner::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     *
     * @return TestResponse
     */
    public function test_get_all_banners(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_banner(): TestResponse
    {
        $id = Banner::firstOrFail()->id;

        return $this->destroy($this->url, $id);
    }

    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/banners/');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'images',
                    'description',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
