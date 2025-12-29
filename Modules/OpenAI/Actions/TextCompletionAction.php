<?php

declare(strict_types=1);

namespace Modules\OpenAI\Actions;

use Modules\OpenAI\Service\OpenAIService;

readonly class TextCompletionAction
{
    public function __construct(private OpenAIService $openAIService) {}

    /**
     * Execute text completion with OpenAI
     */
    public function execute(string $prompt, array $options = []): array
    {
        return $this->openAIService->textCompletion($prompt, $options);
    }
}
