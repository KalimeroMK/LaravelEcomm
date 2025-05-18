<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Post\Models\Post;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class PostTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/posts';

    /**
     * test create product.
     */
    #[Test]
    public function test_create_post(): TestResponse
    {
        $data = Post::factory()->create()->toArray();

        return $this->create($this->url, $data);
    }

    /**
     * test find post.
     */
    #[Test]
    public function test_find_post(): TestResponse
    {
        $post = Post::factory()->create();
        $id = $post->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all posts.
     */
    #[Test]
    public function test_get_all_posts(): TestResponse
    {
        Post::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test update post.
     */
    #[Test]
    public function test_update_post(): TestResponse
    {
        $post = Post::factory()->create();
        $data = [
            'title' => $this->faker->word,
            'status' => 'inactive',
        ];

        $id = $post->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test delete post.
     */
    #[Test]
    public function test_delete_post(): TestResponse
    {
        $post = Post::factory()->create();
        $id = $post->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        Post::factory()->count(2)->create();
        $response = $this->json('GET', '/api/v1/posts');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'summary',
                    'description',
                    'quote',
                    'images', // conditional but we assume it's loaded
                    'tags',
                    'post_cat_id',
                    'status',
                    'created_at',
                    'updated_at',
                    'all_comments_count',
                    'fpost_comments_count',
                    'post_comments_count',
                    'categories_count',
                    'comments_count',
                    'image_url',
                    'post_tag_count',
                    'added_by',
                    'categories', // conditional but we assume it's loaded
                ],
            ],
        ]);
    }
}
