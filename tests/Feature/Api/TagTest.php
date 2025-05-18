<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Tag\Models\Tag;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class TagTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/tags/';

    #[Test]
    public function test_create_tag()
    {
        $data = Tag::factory()->make()->toArray();

        return $this->create($this->url, $data);
    }

    #[Test]
    public function test_update_tag()
    {
        $tag = Tag::factory()->create();
        $data = [
            'title' => $this->faker->unique()->word,
            'status' => 'inactive',
        ];
        $response = $this->updatePUT($this->url, $data, $tag->id);
        $response->assertStatus(200);
        $response->assertJson(['data' => ['id' => $tag->id]]);
    }

    #[Test]
    public function test_find_tag()
    {
        $tag = Tag::factory()->create();
        $response = $this->show($this->url, $tag->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'slug',
                'status',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    #[Test]
    public function test_get_all_tags()
    {
        Tag::factory()->count(2)->create();
        $response = $this->list($this->url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    #[Test]
    public function test_delete_tag()
    {
        $tag = Tag::factory()->create();
        $response = $this->destroy($this->url, $tag->id);
        $response->assertStatus(200);
    }

    #[Test]
    public function test_structure()
    {
        Tag::factory()->count(2)->create();
        $response = $this->json('GET', '/api/v1/tags');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
