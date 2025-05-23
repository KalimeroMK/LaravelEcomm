<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Bundle\Models\Bundle;
use Modules\Product\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class BundleTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/bundles';

    #[Test]
    public function test_create_bundle(): TestResponse
    {
        Storage::fake('public');
        $product = Product::factory()->create();
        $data = [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'products' => [$product->id],
            'images' => [UploadedFile::fake()->image('bundle.jpg')],
        ];

        return $this->create($this->url, $data);
    }

    #[Test]
    public function test_update_bundle(): void
    {
        Storage::fake('public');
        $bundle = Bundle::factory()->create();
        $product = Product::factory()->create();
        $data = [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'products' => [$product->id],
            'images' => [UploadedFile::fake()->image('bundle_update.jpg')],
        ];
        $response = $this->update($this->url, $data, $bundle->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'products',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    #[Test]
    public function test_show_bundle(): void
    {
        $bundle = Bundle::factory()->create();
        $response = $this->show($this->url, $bundle->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'products',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    #[Test]
    public function test_delete_bundle(): void
    {
        $bundle = Bundle::factory()->create();
        $response = $this->destroy($this->url, $bundle->id);
        $response->assertStatus(200);
    }

    #[Test]
    public function test_index_structure(): void
    {
        Bundle::factory()->count(2)->create();
        $response = $this->json('GET', $this->url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'products',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
