<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/newsletters';

    /**
     * test create newsletter.
     */
    #[Test]
    public function test_create_newsletter(): TestResponse
    {
        $data = [
            'email' => $this->faker->unique()->safeEmail,
            'status' => 'active',
            'is_validated' => true,
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_newsletter(): TestResponse
    {
        Newsletter::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_newsletter(): TestResponse
    {
        $newsletter = Newsletter::factory()->create();
        $id = $newsletter->id;

        return $this->destroy($this->url, $id);
    }

    public function test_delete_message(): TestResponse
    {
        $message = Message::factory()->create();
        $id = $message->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        Newsletter::factory()->count(2)->create();
        $response = $this->json('GET', '/api/v1/newsletters');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'email',
                        'token',
                        'is_validated',
                        'created_at',
                        'updated_at',
                    ],
                ],

            ]
        );
    }
}
