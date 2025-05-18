<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Message\Models\Message;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/messages';

    /**
     * test create product.
     */
    #[Test]
    public function test_create_message(): TestResponse
    {
        $message = Message::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'subject' => $this->faker->word,
            'email' => $this->faker->unique()->safeEmail,
            'photo' => $this->faker->word,
            'phone' => $this->faker->phoneNumber,
            'message' => $this->faker->sentence,
            'read_at' => $this->faker->time,
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    #[Test]
    public function test_update_message(): TestResponse
    {
        $message = Message::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'subject' => $this->faker->word,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'message' => $this->faker->sentence,
            'read_at' => $this->faker->time,
        ];

        $id = $message->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_message(): TestResponse
    {
        $message = Message::factory()->create();
        $id = $message->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_message(): TestResponse
    {
        Message::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_message(): TestResponse
    {
        $message = Message::factory()->create();
        $id = $message->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        Message::factory()->count(2)->create();
        $response = $this->json('GET', '/api/v1/messages');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'subject',
                        'email',
                        'photo',
                        'phone',
                        'message',
                        'read_at',
                        'created_at',
                        'updated_at',
                    ],
                ],

            ]
        );
    }
}
