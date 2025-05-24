<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Attribute\Models\Attribute;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class AttributeTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/attributes/';

    #[Test]
    public function create_attribute(): void
    {
        $response = $this->create($this->url, Attribute::factory()->make()->toArray());
        $response->assertStatus(200);
    }

    #[Test]
    public function update_attribute(): void
    {
        $data = Attribute::factory()->make()->toArray();
        $id = Attribute::factory()->create()->id;

        $response = $this->updatePUT($this->url, $data, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function find_attribute(): void
    {
        $id = Attribute::factory()->create()->id;

        $response = $this->show($this->url, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function get_all_attribute(): void
    {
        $response = $this->list($this->url);
        $response->assertStatus(200);
    }

    #[Test]
    public function delete_attribute(): void
    {
        $id = Attribute::factory()->create()->id;

        $response = $this->destroy($this->url, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function structure(): void
    {
        $response = $this->json('GET', '/api/v1/attributes/');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'code',
                    'type',
                    'display',
                    'filterable',
                    'configurable',
                ],
            ],
        ]);
    }

    #[Test]
    public function flexible_attribute_values_are_returned(): void
    {
        $attribute = Attribute::factory()->create([
            'type' => 'text',
        ]);

        $response = $this->json('GET', "/api/v1/attributes/{$attribute->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $attribute->id,
            'type' => 'text',
        ]);
    }
}
