<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\OpenAI\Service\OpenAIService;

readonly class GenerateProductDescriptionAction
{
    public function __construct(private OpenAIService $openAIService) {}

    public function execute(string $title): string
    {
        return $this->openAIService->generateProductDescription($title);
    }
}
