<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Category\Models\Category;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;

    public string $url = '/api/v1/category/';

    use WithFaker;

    /**
     * test create product.
     */
    public function test_create_category(): TestResponse
    {
        $data = [
            'title' => $this->faker->word,
            'status' => 'active',
            'parent_id' => null,
            '_lft' => $this->faker->randomNumber(),
            '_rgt' => $this->faker->randomNumber(),
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    public function test_update_category(): TestResponse
    {
        $data = [
            'parent_id' => 5,
        ];

        $id = Category::firstOrFail()->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    public function test_find_category(): TestResponse
    {
        $id = Category::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    public function test_get_all_category(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    public function test_delete_category(): TestResponse
    {
        $id = Category::firstOrFail()->id;

        return $this->destroy($this->url, $id);
    }

    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/category/');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'title',
                        'slug',
                        'status',
                        '_lft',
                        '_rgt',
                        'created_at',
                        'updated_at',
                        'parent_id',
                    ],
                ],

            ]
        );
    }
}
