<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Coupon\Models\Coupon;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;

    public string $url = '/api/v1/coupon/';

    use WithFaker;

    /**
     * test create product.
     */
    public function test_create_coupon(): TestResponse
    {
        $data = [
            'code' => $this->faker->word,
            'value' => $this->faker->randomFloat(),
            'type' => 'fixed',
            'status' => 'active',
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    public function test_update_coupon(): TestResponse
    {
        $data = [
            'code' => $this->faker->word,
            'value' => $this->faker->randomFloat(),
            'type' => 'percent',
            'status' => 'active',
        ];

        $id = Coupon::firstOrFail()->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    public function test_find_coupon(): TestResponse
    {
        $id = Coupon::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    public function test_get_all_coupon(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    public function test_delete_coupon(): TestResponse
    {
        $id = Coupon::firstOrFail()->id;

        return $this->destroy($this->url, $id);
    }

    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/coupon/');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'code',
                        'type',
                        'value',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],

            ]
        );
    }
}
