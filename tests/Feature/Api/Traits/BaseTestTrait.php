<?php

namespace Tests\Feature\Api\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;

trait BaseTestTrait
{

    /**
     * @param string $url
     *
     * @return TestResponse
     */
    public function list(string $url): TestResponse
    {
        $response = $this->json('GET', $url);
        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

    /**
     * @param string $url
     * @param array $data
     *
     * @return TestResponse
     */
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

    /**
     * @param string $url
     * @param array $data
     * @param int $id
     *
     * @return TestResponse
     */
    public function update(string $url, array $data, int $id): TestResponse
    {
        $response = $this->json(
            'POST',
            $url . $id,
            $data
        );

        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

    /**
     * @param string $url
     * @param array $data
     * @param int $id
     *
     * @return TestResponse
     */
    public function updatePUT(string $url, array $data, int $id): TestResponse
    {
        $response = $this->json(
            'PUT',
            $url . $id,
            $data
        );
        dd($response->getContent());
        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

    /**
     * @param string $url
     * @param int $id
     *
     * @return TestResponse
     */
    public function show(string $url, int $id): TestResponse
    {
        $response = $this->json('GET', $url . $id);
        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

    /**
     * @param string $url
     * @param int $id
     *
     * @return TestResponse
     */
    public function destroy(string $url, int $id): TestResponse
    {
        $response = $this->json('DELETE', $url . $id);

        Log::info(1, [$response->getContent()]);

        return $response->assertStatus(200);
    }

}
