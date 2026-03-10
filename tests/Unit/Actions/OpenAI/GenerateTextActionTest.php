<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\OpenAI;

use Modules\OpenAI\Actions\GenerateTextAction;
use Modules\OpenAI\Service\OpenAIService;
use Tests\Unit\Actions\ActionTestCase;

class GenerateTextActionTest extends ActionTestCase
{
    public function testExecuteGeneratesTextFromPrompt(): void
    {
        $prompt = 'Write a short greeting';

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with(
                $this->callback(function ($messages) use ($prompt) {
                    return count($messages) === 2
                        && $messages[0]['role'] === 'system'
                        && $messages[0]['content'] === 'You are a helpful assistant.'
                        && $messages[1]['role'] === 'user'
                        && $messages[1]['content'] === $prompt;
                }),
                []
            )
            ->willReturn([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Hello! Welcome! How can I assist you today?',
                        ],
                    ],
                ],
            ]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals('Hello! Welcome! How can I assist you today?', $result);
    }

    public function testExecutePassesOptionsToService(): void
    {
        $prompt = 'Generate a creative story';
        $options = ['temperature' => 0.9, 'max_tokens' => 2000];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with($this->anything(), $options)
            ->willReturn([
                'choices' => [
                    ['message' => ['content' => 'Once upon a time...']],
                ],
            ]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt, $options);

        $this->assertEquals('Once upon a time...', $result);
    }

    public function testExecuteReturnsEmptyStringOnMissingContent(): void
    {
        $prompt = 'Test prompt';

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->method('chatCompletion')
            ->willReturn([
                'choices' => [
                    ['message' => []],
                ],
            ]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals('', $result);
    }

    public function testExecuteReturnsEmptyStringOnEmptyChoices(): void
    {
        $prompt = 'Test prompt';

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->method('chatCompletion')
            ->willReturn(['choices' => []]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals('', $result);
    }

    public function testExecuteWithLongPrompt(): void
    {
        $prompt = str_repeat('This is a long prompt. ', 50);

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with(
                $this->callback(function ($messages) use ($prompt) {
                    return $messages[1]['content'] === $prompt;
                }),
                []
            )
            ->willReturn([
                'choices' => [
                    ['message' => ['content' => 'Long response']],
                ],
            ]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals('Long response', $result);
    }

    public function testExecuteWithSpecialCharactersInPrompt(): void
    {
        $prompt = 'What is 2 + 2? Include symbols: @#$%^&*()';

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with(
                $this->callback(function ($messages) use ($prompt) {
                    return $messages[1]['content'] === $prompt;
                }),
                []
            )
            ->willReturn([
                'choices' => [
                    ['message' => ['content' => '2 + 2 = 4']],
                ],
            ]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals('2 + 2 = 4', $result);
    }

    public function testExecuteWithMultilinePrompt(): void
    {
        $prompt = "Line 1\nLine 2\nLine 3";

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with(
                $this->callback(function ($messages) use ($prompt) {
                    return $messages[1]['content'] === $prompt;
                }),
                []
            )
            ->willReturn([
                'choices' => [
                    ['message' => ['content' => "Response line 1\nResponse line 2"]],
                ],
            ]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals("Response line 1\nResponse line 2", $result);
    }

    public function testExecuteWithEmptyPrompt(): void
    {
        $prompt = '';

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with(
                $this->callback(function ($messages) use ($prompt) {
                    return $messages[1]['content'] === $prompt;
                }),
                []
            )
            ->willReturn([
                'choices' => [
                    ['message' => ['content' => 'How can I help?']],
                ],
            ]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals('How can I help?', $result);
    }

    public function testExecuteWithUnicodeCharacters(): void
    {
        $prompt = 'Say hello in different languages: 你好, مرحبا, 🌍';

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->method('chatCompletion')
            ->willReturn([
                'choices' => [
                    ['message' => ['content' => 'Hello! 你好! مرحبا! 🌍']],
                ],
            ]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt);

        $this->assertEquals('Hello! 你好! مرحبا! 🌍', $result);
    }

    public function testExecuteWithModelOption(): void
    {
        $prompt = 'Test';
        $options = ['model' => 'gpt-4-turbo'];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with($this->anything(), $options)
            ->willReturn([
                'choices' => [
                    ['message' => ['content' => 'Response']],
                ],
            ]);

        $action = new GenerateTextAction($mockService);
        $result = $action->execute($prompt, $options);

        $this->assertEquals('Response', $result);
    }
}
