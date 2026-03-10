<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\OpenAI;

use Modules\OpenAI\Actions\TextCompletionAction;
use Modules\OpenAI\Service\OpenAIService;
use Tests\Unit\Actions\ActionTestCase;

class TextCompletionActionTest extends ActionTestCase
{
    public function testExecuteCallsTextCompletionService(): void
    {
        $prompt = 'Complete this sentence: The quick brown fox';

        $expectedResponse = [
            'id' => 'cmpl-123',
            'object' => 'text_completion',
            'choices' => [
                [
                    'text' => ' jumps over the lazy dog.',
                    'index' => 0,
                    'logprobs' => null,
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 10,
                'completion_tokens' => 10,
                'total_tokens' => 20,
            ],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('textCompletion')
            ->with($prompt, [])
            ->willReturn($expectedResponse);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testExecutePassesOptions(): void
    {
        $prompt = 'Write a poem';
        $options = [
            'model' => 'text-davinci-003',
            'max_tokens' => 1500,
            'temperature' => 0.8,
            'top_p' => 0.9,
        ];

        $expectedResponse = [
            'choices' => [
                ['text' => 'Roses are red, violets are blue...'],
            ],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('textCompletion')
            ->with($prompt, $options)
            ->willReturn($expectedResponse);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt, $options);

        $this->assertEquals('Roses are red, violets are blue...', $result['choices'][0]['text']);
    }

    public function testExecuteReturnsFullResponse(): void
    {
        $prompt = 'Test prompt';

        $fullResponse = [
            'id' => 'cmpl-test456',
            'object' => 'text_completion',
            'created' => 1234567890,
            'model' => 'text-davinci-003',
            'choices' => [
                [
                    'text' => 'Test completion',
                    'index' => 0,
                    'logprobs' => null,
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 5,
                'completion_tokens' => 5,
                'total_tokens' => 10,
            ],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->method('textCompletion')->willReturn($fullResponse);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('choices', $result);
        $this->assertArrayHasKey('usage', $result);
        $this->assertEquals('cmpl-test456', $result['id']);
        $this->assertEquals('text-davinci-003', $result['model']);
    }

    public function testExecuteWithEmptyOptions(): void
    {
        $prompt = 'Simple test';

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('textCompletion')
            ->with($prompt, [])
            ->willReturn(['choices' => [['text' => 'Result']]]);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt, []);

        $this->assertEquals('Result', $result['choices'][0]['text']);
    }

    public function testExecuteWithLongPrompt(): void
    {
        $prompt = str_repeat('This is a test prompt. ', 100);

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('textCompletion')
            ->with($prompt, [])
            ->willReturn([
                'choices' => [
                    ['text' => 'Long prompt processed successfully.'],
                ],
            ]);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals('Long prompt processed successfully.', $result['choices'][0]['text']);
    }

    public function testExecuteWithMultipleChoices(): void
    {
        $prompt = 'Generate ideas';

        $responseWithMultipleChoices = [
            'choices' => [
                ['text' => 'Idea 1', 'index' => 0],
                ['text' => 'Idea 2', 'index' => 1],
                ['text' => 'Idea 3', 'index' => 2],
            ],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->method('textCompletion')
            ->willReturn($responseWithMultipleChoices);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt);

        $this->assertCount(3, $result['choices']);
        $this->assertEquals('Idea 1', $result['choices'][0]['text']);
        $this->assertEquals('Idea 2', $result['choices'][1]['text']);
        $this->assertEquals('Idea 3', $result['choices'][2]['text']);
    }

    public function testExecuteWithEmptyPrompt(): void
    {
        $prompt = '';

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('textCompletion')
            ->with($prompt, [])
            ->willReturn([
                'choices' => [
                    ['text' => 'Default completion for empty prompt'],
                ],
            ]);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals('Default completion for empty prompt', $result['choices'][0]['text']);
    }

    public function testExecuteWithCodePrompt(): void
    {
        $prompt = 'function greet() {';

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('textCompletion')
            ->with($prompt, [])
            ->willReturn([
                'choices' => [
                    [
                        'text' => "\n    return 'Hello, World!';\n}",
                        'finish_reason' => 'stop',
                    ],
                ],
            ]);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt);

        $this->assertStringContainsString('return', $result['choices'][0]['text']);
    }

    public function testExecuteWithDifferentModels(): void
    {
        $prompt = 'Test';
        $models = ['text-davinci-003', 'text-davinci-002', 'text-curie-001'];

        foreach ($models as $model) {
            $mockService = $this->createMock(OpenAIService::class);
            $mockService->expects($this->once())
                ->method('textCompletion')
                ->with($prompt, ['model' => $model])
                ->willReturn([
                    'model' => $model,
                    'choices' => [['text' => "Response from {$model}"]],
                ]);

            $action = new TextCompletionAction($mockService);
            $result = $action->execute($prompt, ['model' => $model]);

            $this->assertEquals($model, $result['model']);
        }
    }

    public function testExecuteWithTemperatureZero(): void
    {
        $prompt = 'Consistent output test';
        $options = ['temperature' => 0.0];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('textCompletion')
            ->with($prompt, $options)
            ->willReturn([
                'choices' => [
                    ['text' => 'Deterministic response'],
                ],
            ]);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt, $options);

        $this->assertEquals('Deterministic response', $result['choices'][0]['text']);
    }

    public function testExecuteWithMaxTokens(): void
    {
        $prompt = 'Write a long story';
        $options = ['max_tokens' => 100];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('textCompletion')
            ->with($prompt, $options)
            ->willReturn([
                'choices' => [
                    [
                        'text' => 'Short story due to token limit...',
                        'finish_reason' => 'length',
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 10,
                    'completion_tokens' => 100,
                    'total_tokens' => 110,
                ],
            ]);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt, $options);

        $this->assertEquals('length', $result['choices'][0]['finish_reason']);
        $this->assertEquals(100, $result['usage']['completion_tokens']);
    }

    public function testExecuteWithStopSequence(): void
    {
        $prompt = 'Count: 1, 2, 3,';
        $options = ['stop' => ['5']];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('textCompletion')
            ->with($prompt, $options)
            ->willReturn([
                'choices' => [
                    [
                        'text' => ' 4, ',
                        'finish_reason' => 'stop',
                    ],
                ],
            ]);

        $action = new TextCompletionAction($mockService);
        $result = $action->execute($prompt, $options);

        $this->assertEquals('stop', $result['choices'][0]['finish_reason']);
    }
}
