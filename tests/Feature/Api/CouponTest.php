<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Coupon\Models\Coupon;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/coupons';

    /**
     * test create product.
     */
    #[Test]
    public function test_create_coupon(): TestResponse
    {
        $coupon = Coupon::factory()->create();
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
    #[Test]
    public function test_update_coupon(): TestResponse
    {
        $coupon = Coupon::factory()->create();
        $data = [
            'code' => $this->faker->word,
            'value' => $this->faker->randomFloat(),
            'type' => 'percent',
            'status' => 'active',
        ];

        $id = $coupon->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_coupon(): TestResponse
    {
        $coupon = Coupon::factory()->create();
        $id = $coupon->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_coupon(): TestResponse
    {
        Coupon::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_coupon(): TestResponse
    {
        $coupon = Coupon::factory()->create();
        $id = $coupon->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        Coupon::factory()->count(2)->create();
        $response = $this->json('GET', '/api/v1/coupons');
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
