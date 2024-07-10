<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Tag\Models\Tag;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class TagTest extends TestCase
{
    use BaseTestTrait;

    public string $url = '/api/v1/tag/';

    use WithFaker;
    use WithoutMiddleware;

    /**
     * test create product.
     */
    public function test_create_size(): TestResponse
    {
        Storage::fake('uploads');

        $data = [
            'name' => Carbon::now().$this->faker->unique()->word,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    public function test_update_size(): TestResponse
    {
        $data = [
            'name' => Carbon::now().$this->faker->unique()->word,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        $id = Tag::firstOrFail()->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    public function test_find_size(): TestResponse
    {
        $id = Tag::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    public function test_get_all_size(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    public function test_delete_size(): TestResponse
    {
        $id = Tag::firstOrFail()->id;

        return $this->destroy($this->url, $id);
    }

    public function test_structure(): void
    {
        $response = $this->json('GET', '/api/v1/tag/');
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
