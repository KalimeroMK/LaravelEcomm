<?php

declare(strict_types=1);

namespace Modules\OpenAI\Actions;

use Modules\OpenAI\Service\OpenAIService;

readonly class GenerateTextAction
{
    public function __construct(private OpenAIService $openAIService) {}

    /**
     * Generate text using OpenAI chat completions
     */
    public function execute(string $prompt, array $options = []): string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $response = $this->openAIService->chatCompletion($messages, $options);

        return $response['choices'][0]['message']['content'] ?? '';
    }
}
