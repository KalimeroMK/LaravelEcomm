<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\OpenAI\Service\OpenAIService;
use Modules\Product\Actions\GenerateProductDescriptionAction;
use Tests\Unit\Actions\ActionTestCase;

class GenerateProductDescriptionActionTest extends ActionTestCase
{
    public function testExecuteGeneratesDescription(): void
    {
        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('generateProductDescription')
            ->with('Test Product')
            ->willReturn('This is a generated description for Test Product.');

        $this->app->instance(OpenAIService::class, $mockService);

        $action = app(GenerateProductDescriptionAction::class);
        $result = $action->execute('Test Product');

        $this->assertEquals('This is a generated description for Test Product.', $result);
    }

    public function testExecutePassesTitleToService(): void
    {
        $mockService = $this->createMock(OpenAIService::class);
        $mockService->expects($this->once())
            ->method('generateProductDescription')
            ->with('Wireless Bluetooth Headphones')
            ->willReturn('Premium wireless audio experience.');

        $this->app->instance(OpenAIService::class, $mockService);

        $action = app(GenerateProductDescriptionAction::class);
        $result = $action->execute('Wireless Bluetooth Headphones');

        $this->assertEquals('Premium wireless audio experience.', $result);
    }

    public function testExecuteReturnsEmptyStringWhenServiceReturnsEmpty(): void
    {
        $mockService = $this->createMock(OpenAIService::class);
        $mockService->method('generateProductDescription')
            ->willReturn('');

        $this->app->instance(OpenAIService::class, $mockService);

        $action = app(GenerateProductDescriptionAction::class);
        $result = $action->execute('Some Product');

        $this->assertEquals('', $result);
    }
}
