<?php

declare(strict_types=1);

namespace Modules\OpenAI\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\OpenAI\Actions\ChatCompletionAction;
use Modules\OpenAI\Actions\GenerateTextAction;
use Modules\OpenAI\Actions\TextCompletionAction;
use Modules\OpenAI\Http\Requests\OpenAIRequest;

class OpenAIController extends CoreController
{
    public function __construct(
        private readonly GenerateTextAction $generateTextAction,
        private readonly ChatCompletionAction $chatCompletionAction,
        private readonly TextCompletionAction $textCompletionAction
    ) {}

    /**
     * Generate text from prompt
     */
    public function generateText(OpenAIRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $prompt = $validated['prompt'];

        $options = array_filter([
            'model' => $validated['model'] ?? 'gpt-3.5-turbo',
            'max_tokens' => $validated['max_tokens'] ?? null,
            'temperature' => $validated['temperature'] ?? null,
            'top_p' => $validated['top_p'] ?? null,
            'frequency_penalty' => $validated['frequency_penalty'] ?? null,
            'presence_penalty' => $validated['presence_penalty'] ?? null,
            'stop' => $validated['stop'] ?? null,
        ], fn ($value) => $value !== null);

        $text = $this->generateTextAction->execute($prompt, $options);

        return $this
            ->setMessage('Text generated successfully.')
            ->respond(['text' => $text]);
    }

    /**
     * Chat completion
     */
    public function chatCompletion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string|in:system,user,assistant',
            'messages.*.content' => 'required|string',
            'model' => 'nullable|string|in:gpt-3.5-turbo,gpt-4,gpt-4-turbo',
            'max_tokens' => 'nullable|integer|min:1|max:4096',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'top_p' => 'nullable|numeric|min:0|max:1',
            'frequency_penalty' => 'nullable|numeric|min:-2|max:2',
            'presence_penalty' => 'nullable|numeric|min:-2|max:2',
            'stop' => 'nullable|array|max:4',
            'stop.*' => 'string|max:200',
        ]);

        $messages = $validated['messages'];
        $options = array_filter([
            'model' => $validated['model'] ?? null,
            'max_tokens' => $validated['max_tokens'] ?? null,
            'temperature' => $validated['temperature'] ?? null,
            'top_p' => $validated['top_p'] ?? null,
            'frequency_penalty' => $validated['frequency_penalty'] ?? null,
            'presence_penalty' => $validated['presence_penalty'] ?? null,
            'stop' => $validated['stop'] ?? null,
        ], fn ($value) => $value !== null);

        $response = $this->chatCompletionAction->execute($messages, $options);

        return $this
            ->setMessage('Chat completion generated successfully.')
            ->respond($response);
    }

    /**
     * Text completion
     */
    public function textCompletion(OpenAIRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $prompt = $validated['prompt'];

        $options = array_filter([
            'model' => $validated['model'] ?? null,
            'max_tokens' => $validated['max_tokens'] ?? null,
            'temperature' => $validated['temperature'] ?? null,
            'top_p' => $validated['top_p'] ?? null,
            'frequency_penalty' => $validated['frequency_penalty'] ?? null,
            'presence_penalty' => $validated['presence_penalty'] ?? null,
            'stop' => $validated['stop'] ?? null,
        ], fn ($value) => $value !== null);

        $response = $this->textCompletionAction->execute($prompt, $options);

        return $this
            ->setMessage('Text completion generated successfully.')
            ->respond($response);
    }
}
