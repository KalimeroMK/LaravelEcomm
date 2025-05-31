<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Category\Models\Category;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/categories';

    /**
     * test create product.
     */
    #[Test]
    public function test_create_category(): TestResponse
    {
        $category = Category::factory()->create();
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
    // #[Test]
    public function test_update_category(): TestResponse
    {
        $category = Category::factory()->create();
        $parentCategory = Category::factory()->create();

        $data = [
            'parent_id' => $parentCategory->id,
        ];

        return $this->updatePUT($this->url, $data, $category->id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_category(): TestResponse
    {
        $category = Category::factory()->create();
        $id = $category->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_category(): TestResponse
    {
        Category::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_category(): TestResponse
    {
        $category = Category::factory()->create();
        $id = $category->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        Category::factory()->count(2)->create();
        $response = $this->json('GET', '/api/v1/categories');
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
