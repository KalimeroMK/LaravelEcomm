<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;

trait BaseTestTrait
{
    public function list(string $url): TestResponse
    {
        $response = $this->json('GET', $url);
        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

    public function create(string $url, array $data): TestResponse
    {
        $response = $this->json(
            'POST',
            $url,
            $data
        );
        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

    public function update(string $url, array $data, int $id): TestResponse
    {
        $response = $this->json(
            'POST',
            rtrim($url, '/').'/'.$id,
            $data
        );
        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

    public function updatePUT(string $url, array $data, int $id): TestResponse
    {
        $response = $this->json(
            'PUT',
            rtrim($url, '/').'/'.$id,
            $data
        );
        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

    public function show(string $url, int $id): TestResponse
    {
        $response = $this->json('GET', rtrim($url, '/').'/'.$id);
        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

    public function destroy(string $url, int $id): TestResponse
    {
        $response = $this->json('DELETE', rtrim($url, '/').'/'.$id);

        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }
}
