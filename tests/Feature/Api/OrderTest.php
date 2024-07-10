<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\TestResponse;
use Modules\Order\Models\Order;
use Modules\Shipping\Models\Shipping;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;

    public string $url = '/api/v1/order/';

    use WithFaker;

    /**
     * test create product.
     */
    public function test_create_order(): TestResponse
    {
        $data = [
            'order_number' => $this->faker->unique()->numberBetween(1, 9999),
            'sub_total' => $this->faker->numberBetween(1, 500),
            'coupon' => $this->faker->numberBetween(1, 5),
            'total_amount' => $this->faker->numberBetween(1, 500),
            'quantity' => $this->faker->numberBetween(1, 500),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'country' => $this->faker->country,
            'post_code' => $this->faker->word,
            'address1' => $this->faker->address,
            'address2' => $this->faker->address,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => Auth::id(),
            'shipping_id' => function () {
                return Shipping::factory()->create()->id;
            },
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    public function test_update_order(): TestResponse
    {
        $data = [
            'status' => 'process',
        ];

        $id = (int) Order::firstOrFail()->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    public function test_find_order(): TestResponse
    {
        $id = Order::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    public function test_get_all_order(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    public function test_delete_order(): TestResponse
    {
        $id = Order::firstOrFail()->id;

        return $this->destroy($this->url, $id);
    }

    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/order/');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'order_number',
                        'sub_total',
                        'coupon',
                        'total_amount',
                        'quantity',
                        'payment_method',
                        'payment_status',
                        'status',
                        'first_name',
                        'last_name',
                        'email',
                        'phone',
                        'country',
                        'post_code',
                        'address1',
                        'address2',
                        'created_at',
                        'updated_at',
                        'cart_info_count',
                        'carts_count',
                        'user_id',
                        'shipping_id',
                    ],
                ],

            ]
        );
    }
}
