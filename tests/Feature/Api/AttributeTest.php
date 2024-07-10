<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Attribute\Models\Attribute;
use Modules\Banner\Models\Banner;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class AttributeTest extends TestCase
{
    use BaseTestTrait;

    public string $url = '/api/v1/attributes/';

    use WithFaker;
    use WithoutMiddleware;

    /**
     * test create product.
     */
    public function test_create_attribute(): TestResponse
    {
        return $this->create($this->url, Attribute::factory()->make()->toArray());
    }

    /**
     * test update product.
     */
    public function test_update_attribute(): TestResponse
    {
        $data = [
            'name' => $this->faker->name(),
            'code' => $this->faker->name(),
        ];
        $id = Attribute::factory()->create()->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    public function test_find_attribute(): TestResponse
    {
        $id = Banner::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    public function test_get_all_attribute(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    public function test_delete_attribute(): TestResponse
    {
        $id = Banner::firstOrFail()->id;

        return $this->destroy($this->url, $id);
    }

    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/attributes/');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'code',
                    'type',
                    'display',
                    'filterable',
                    'configurable',
                ],
            ],
        ]);
    }
}
