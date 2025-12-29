<?php

declare(strict_types=1);

namespace Modules\OpenAI\Actions;

use Modules\OpenAI\Service\OpenAIService;

readonly class ChatCompletionAction
{
    public function __construct(private OpenAIService $openAIService) {}

    /**
     * Execute chat completion with OpenAI
     */
    public function execute(array $messages, array $options = []): array
    {
        return $this->openAIService->chatCompletion($messages, $options);
    }
}
