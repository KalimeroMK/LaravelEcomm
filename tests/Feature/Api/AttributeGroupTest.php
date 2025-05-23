<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Modules\Attribute\Models\AttributeGroup;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class AttributeGroupTest extends TestCase
{
    use BaseTestTrait;
    use WithFaker;
    use WithoutMiddleware;

    public string $url = '/api/v1/attribute-groups/';

    #[Test]
    public function create_attribute_group(): TestResponse
    {
        $data = [
            'name' => 'mame-'.mb_strtoupper(Str::random(10)),
        ];

        return $this->create($this->url, $data);
    }

    #[Test]
    public function update_attribute_group(): void
    {
        $data = [
            'name' => 'mame-'.mb_strtoupper(Str::random(10)),
        ];
        $id = AttributeGroup::factory()->create()->id;

        $response = $this->updatePUT($this->url, $data, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function find_attribute_group(): void
    {
        $id = AttributeGroup::factory()->create()->id;

        $response = $this->show($this->url, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function get_all_attribute_group(): void
    {
        $response = $this->list($this->url);
        $response->assertStatus(200);
    }

    #[Test]
    public function delete_attribute_group(): void
    {
        $id = AttributeGroup::factory()->create()->id;

        $response = $this->destroy($this->url, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function structure_group(): void
    {
        $response = $this->json('GET', $this->url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
