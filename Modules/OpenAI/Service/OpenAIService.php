<?php

declare(strict_types=1);

namespace Modules\OpenAI\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenAIService
{
    protected Client $client;

    protected string $apiKey;

    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->client = new Client;
        $this->apiKey = config('openai.openai.api_key', env('OPENAI_API_KEY')) ?? '';
    }

    /**
     * Generate product description
     */
    public function generateProductDescription(string $productTitle): string
    {
        $response = $this->client->post("{$this->baseUrl}/chat/completions", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => "Generate a product description for: $productTitle"],
                ],
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);

        return $body['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Generate text completion using chat completions API
     *
     * @throws GuzzleException
     */
    public function chatCompletion(array $messages, array $options = []): array
    {
        $defaultOptions = [
            'model' => 'gpt-3.5-turbo',
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ];

        $payload = array_merge($defaultOptions, $options, [
            'messages' => $messages,
        ]);

        $response = $this->client->post("{$this->baseUrl}/chat/completions", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Generate text completion using text completions API
     *
     * @throws GuzzleException
     */
    public function textCompletion(string $prompt, array $options = []): array
    {
        $defaultOptions = [
            'model' => 'text-davinci-003',
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ];

        $payload = array_merge($defaultOptions, $options, [
            'prompt' => $prompt,
        ]);

        $response = $this->client->post("{$this->baseUrl}/completions", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
