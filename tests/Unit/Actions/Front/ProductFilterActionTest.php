<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Front\Actions\ProductFilterAction;
use Tests\Unit\Actions\ActionTestCase;

class ProductFilterActionTest extends ActionTestCase
{
    public function test_execute_returns_filter_url(): void
    {
        // Arrange
        $action = app(ProductFilterAction::class);
        $data = [
            'show' => 12,
            'sortBy' => 'price',
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertIsString($result);
        $this->assertStringContainsString('show=12', $result);
        $this->assertStringContainsString('sortBy=price', $result);
    }

    public function test_execute_builds_grids_route(): void
    {
        // Arrange
        $action = app(ProductFilterAction::class);
        $data = [];

        // Act
        $result = $action->execute($data, 'product-grids');

        // Assert
        $this->assertStringContainsString('product-grids', $result);
    }

    public function test_execute_builds_lists_route(): void
    {
        // Arrange
        $action = app(ProductFilterAction::class);
        $data = [];

        // Act
        $result = $action->execute($data, 'product-lists');

        // Assert
        $this->assertStringContainsString('product-lists', $result);
    }

    public function test_execute_includes_category_filter(): void
    {
        // Arrange
        $action = app(ProductFilterAction::class);
        $data = [
            'category' => ['electronics', 'phones'],
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertStringContainsString('category=', $result);
        $this->assertStringContainsString('electronics', $result);
        $this->assertStringContainsString('phones', $result);
    }

    public function test_execute_includes_brand_filter(): void
    {
        // Arrange
        $action = app(ProductFilterAction::class);
        $data = [
            'brand' => ['nike', 'adidas'],
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertStringContainsString('brand=', $result);
        $this->assertStringContainsString('nike', $result);
        $this->assertStringContainsString('adidas', $result);
    }

    public function test_execute_includes_price_range(): void
    {
        // Arrange
        $action = app(ProductFilterAction::class);
        $data = [
            'price_range' => '10-100',
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertStringContainsString('price=10-100', $result);
    }

    public function test_execute_excludes_empty_values(): void
    {
        // Arrange
        $action = app(ProductFilterAction::class);
        $data = [
            'show' => null,
            'sortBy' => '',
            'category' => [],
            'brand' => [],
            'price_range' => null,
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertStringNotContainsString('show=', $result);
        $this->assertStringNotContainsString('sortBy=', $result);
        $this->assertStringNotContainsString('category=', $result);
        $this->assertStringNotContainsString('brand=', $result);
        $this->assertStringNotContainsString('price=', $result);
    }
}
