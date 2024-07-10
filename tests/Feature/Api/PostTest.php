<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Post\Models\Post;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class PostTest extends TestCase
{
    use BaseTestTrait;
    use WithoutMiddleware;

    public string $url = '/api/v1/post/';

    use WithFaker;

    /**
     * test create product.
     */
    public function test_create_post(): TestResponse
    {
        Storage::fake('uploads');

        $data = [
            'title' => $this->faker->word,
            'slug' => $this->faker->slug,
            'status' => 'active',
            'summary' => $this->faker->text,
            'category[]' => [1, 2, 3, 4],
            'photo' => UploadedFile::fake()->image('file.png', 600, 600),
            'description' => $this->faker->text,
            'quote' => $this->faker->word,
            'tags' => $this->faker->word,
            'added_by' => $this->faker->numberBetween(1, 3),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test find product.
     */
    public function test_find_post(): TestResponse
    {
        $id = Post::firstOrFail()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    public function test_get_all_post(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    public function test_delete_post(): TestResponse
    {
        $id = Post::firstOrFail()->id;

        return $this->destroy($this->url, $id);
    }

    public function test_structure()
    {
        $response = $this->json('GET', '/api/v1/post/');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'title',
                        'slug',
                        'summary',
                        'description',
                        'quote',
                        'photo',
                        'tags',
                        'post_cat_id',
                        'post_tag_id',
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
                    ],
                ],

            ]
        );
    }
}
