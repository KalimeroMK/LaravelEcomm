<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Shipping\Models\Shipping;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;

    public string $url = '/api/v1/shipping/';

    use WithFaker;

    /**
     * test create product.
     */
    public function test_create_shipping(): TestResponse
    {
        Storage::fake('uploads');

        $data = [
            'type' => Carbon::now().$this->faker->unique()->word,
            'price' => $this->faker->numberBetween(1, 100),
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    public function test_update_shipping(): TestResponse
    {
        $data = [
            'type' => Carbon::now().$this->faker->unique()->word,
            'price' => $this->faker->numberBetween(1, 100),
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        $id = Shipping::firstOrFail()->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    public function test_find_shipping(): TestResponse
    {
        $id = Shipping::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    public function test_get_all_shipping(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    public function test_delete_shipping(): TestResponse
    {
        $id = Shipping::firstOrFail()->id;

        return $this->destroy($this->url, $id);
    }

    public function test_structure(): void
    {
        $response = $this->json('GET', '/api/v1/shipping/');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'type',
                        'price',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],

            ]
        );
    }
}
