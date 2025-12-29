<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;
use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;
use Modules\Product\Actions\GetProductFormDataAction;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;
use Tests\TestCase;

class GetProductFormDataActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_returns_all_required_form_data(): void
    {
        // Arrange
        Brand::factory()->count(2)->create();
        Category::factory()->count(3)->create();
        Tag::factory()->count(2)->create();
        Attribute::factory()->count(2)->create();

        $action = new GetProductFormDataAction(
            new BrandRepository(),
            new CategoryRepository(),
            new TagRepository(),
            new AttributeRepository()
        );

        // Act
        $result = $action->execute();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('brands', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertCount(2, $result['brands']);
        $this->assertCount(3, $result['categories']);
        $this->assertCount(2, $result['tags']);
        $this->assertCount(2, $result['attributes']);
    }
}
