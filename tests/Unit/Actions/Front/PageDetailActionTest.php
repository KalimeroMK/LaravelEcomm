<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Modules\Front\Actions\PageDetailAction;
use Modules\Page\Models\Page;
use Tests\Unit\Actions\ActionTestCase;

class PageDetailActionTest extends ActionTestCase
{
    public function test_invoke_returns_page_by_slug(): void
    {
        // Arrange
        $page = Page::factory()->create([
            'slug' => 'about-us',
            'title' => 'About Us',
        ]);

        $action = app(PageDetailAction::class);

        // Act
        $result = $action('about-us');

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('page', $result);
        $this->assertEquals($page->id, $result['page']->id);
        $this->assertEquals('About Us', $result['page']->title);
    }

    public function test_invoke_returns_null_for_nonexistent_page(): void
    {
        // Arrange
        $action = app(PageDetailAction::class);

        // Act
        $result = $action('nonexistent-page');

        // Assert
        $this->assertIsArray($result);
        $this->assertNull($result['page']);
    }

    public function test_invoke_returns_correct_page_data(): void
    {
        // Arrange
        Page::factory()->create([
            'slug' => 'contact',
            'title' => 'Contact Us',
            'content' => 'Contact us at email@example.com',
        ]);

        $action = app(PageDetailAction::class);

        // Act
        $result = $action('contact');

        // Assert
        $this->assertEquals('Contact Us', $result['page']->title);
        $this->assertEquals('Contact us at email@example.com', $result['page']->content);
    }

    public function test_invoke_uses_cache(): void
    {
        // Arrange
        Page::factory()->create([
            'slug' => 'cached-page',
            'title' => 'Cached Page',
        ]);

        $action = app(PageDetailAction::class);

        // Act
        $result1 = $action('cached-page');
        $result2 = $action('cached-page');

        // Assert
        $this->assertEquals($result1['page']->id, $result2['page']->id);
    }

    public function test_invoke_different_slugs_return_different_pages(): void
    {
        // Arrange
        $page1 = Page::factory()->create(['slug' => 'page-one']);
        $page2 = Page::factory()->create(['slug' => 'page-two']);

        $action = app(PageDetailAction::class);

        // Act
        $result1 = $action('page-one');
        $result2 = $action('page-two');

        // Assert
        $this->assertEquals($page1->id, $result1['page']->id);
        $this->assertEquals($page2->id, $result2['page']->id);
    }
}
