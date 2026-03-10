<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\OpenAI;

use Modules\OpenAI\Actions\ChatCompletionAction;
use Modules\OpenAI\Service\OpenAIService;
use Tests\Unit\Actions\ActionTestCase;

class ChatCompletionActionTest extends ActionTestCase
{
    public function testExecuteCallsOpenAIService(): void
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => 'Hello!'],
        ];

        $expectedResponse = [
            'id' => 'chatcmpl-123',
            'object' => 'chat.completion',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Hello! How can I help you today?',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with($messages, [])
            ->willReturn($expectedResponse);

        $action = new ChatCompletionAction($mockService);
        $result = $action->execute($messages);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testExecutePassesOptions(): void
    {
        $messages = [
            ['role' => 'user', 'content' => 'Tell me a joke'],
        ];

        $options = [
            'model' => 'gpt-4',
            'max_tokens' => 500,
            'temperature' => 0.9,
        ];

        $expectedResponse = [
            'choices' => [
                ['message' => ['content' => 'Why did the chicken cross the road?']],
            ],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with($messages, $options)
            ->willReturn($expectedResponse);

        $action = new ChatCompletionAction($mockService);
        $result = $action->execute($messages, $options);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testExecuteWithMultipleMessages(): void
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => 'What is the capital of France?'],
            ['role' => 'assistant', 'content' => 'The capital of France is Paris.'],
            ['role' => 'user', 'content' => 'What is the population?'],
        ];

        $expectedResponse = [
            'choices' => [
                [
                    'message' => [
                        'content' => 'Paris has a population of approximately 2.1 million people.',
                    ],
                ],
            ],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with($messages, [])
            ->willReturn($expectedResponse);

        $action = new ChatCompletionAction($mockService);
        $result = $action->execute($messages);

        $this->assertEquals('Paris has a population of approximately 2.1 million people.', $result['choices'][0]['message']['content']);
    }

    public function testExecuteWithEmptyOptions(): void
    {
        $messages = [['role' => 'user', 'content' => 'Hi']];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with($messages, [])
            ->willReturn(['choices' => []]);

        $action = new ChatCompletionAction($mockService);
        $action->execute($messages, []);
    }

    public function testExecuteReturnsFullResponseStructure(): void
    {
        $messages = [['role' => 'user', 'content' => 'Test']];

        $fullResponse = [
            'id' => 'chatcmpl-test123',
            'object' => 'chat.completion',
            'created' => 1234567890,
            'model' => 'gpt-3.5-turbo',
            'usage' => [
                'prompt_tokens' => 10,
                'completion_tokens' => 20,
                'total_tokens' => 30,
            ],
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Test response',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->method('chatCompletion')->willReturn($fullResponse);

        $action = new ChatCompletionAction($mockService);
        $result = $action->execute($messages);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('usage', $result);
        $this->assertArrayHasKey('choices', $result);
        $this->assertEquals('chatcmpl-test123', $result['id']);
        $this->assertEquals(30, $result['usage']['total_tokens']);
    }

    public function testExecuteWithSystemMessageOnly(): void
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are an expert in Laravel.'],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with($messages, [])
            ->willReturn(['choices' => [['message' => ['content' => 'Ready to help with Laravel!']]]]);

        $action = new ChatCompletionAction($mockService);
        $result = $action->execute($messages);

        $this->assertEquals('Ready to help with Laravel!', $result['choices'][0]['message']['content']);
    }

    public function testExecuteWithFunctionCallingOptions(): void
    {
        $messages = [['role' => 'user', 'content' => 'What is the weather?']];

        $options = [
            'functions' => [
                [
                    'name' => 'get_weather',
                    'description' => 'Get the current weather',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'location' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
            'function_call' => 'auto',
        ];

        $expectedResponse = [
            'choices' => [
                [
                    'message' => [
                        'role' => 'assistant',
                        'content' => null,
                        'function_call' => [
                            'name' => 'get_weather',
                            'arguments' => '{"location":"current"}',
                        ],
                    ],
                ],
            ],
        ];

        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('chatCompletion')
            ->with($messages, $options)
            ->willReturn($expectedResponse);

        $action = new ChatCompletionAction($mockService);
        $result = $action->execute($messages, $options);

        $this->assertEquals('get_weather', $result['choices'][0]['message']['function_call']['name']);
    }
}
