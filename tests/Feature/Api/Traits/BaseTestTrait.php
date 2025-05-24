<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Traits;

use Illuminate\Testing\TestResponse;

trait BaseTestTrait
{
    public function list(string $url): TestResponse
    {
        $response = $this->json('GET', $url);

        return $response->assertStatus(200);
    }

    public function create(string $url, array $data): TestResponse
    {
        $response = $this->json('POST', $url, $data);

        return $response
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function update(string $url, array $data, int $id): TestResponse
    {
        $uri = mb_rtrim($url, '/').'/'.$id;
        $response = $this->json('POST', $uri, $data);

        return $response
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function updatePUT(string $url, array $data, int $id): TestResponse
    {
        $uri = mb_rtrim($url, '/').'/'.$id;
        $response = $this->json('PUT', $uri, $data);
        return $response
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function show(string $url, int $id): TestResponse
    {
        $uri = mb_rtrim($url, '/').'/'.$id;
        $response = $this->json('GET', $uri);

        return $response
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function destroy(string $url, int $id): TestResponse
    {
        $uri = mb_rtrim($url, '/').'/'.$id;
        $response = $this->json('DELETE', $uri);

        return $response->assertStatus(200); // No Content
    }
}
