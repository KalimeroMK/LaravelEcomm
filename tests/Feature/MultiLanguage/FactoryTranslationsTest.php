<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Category\Models\Category;
use Modules\Page\Models\Page;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

uses(RefreshDatabase::class);

test('product factory can create translations', function () {
    $product = Product::factory()
        ->withTranslations(['en', 'mk', 'de'])
        ->create();

    expect($product->translations)->toHaveCount(3);
    expect($product->translations->pluck('locale')->toArray())->toContain('en', 'mk', 'de');
    expect($product->translation('en'))->not->toBeNull();
    expect($product->translation('mk'))->not->toBeNull();
    expect($product->translation('de'))->not->toBeNull();
});

test('category factory can create translations', function () {
    $category = Category::factory()
        ->withTranslations(['en', 'mk'])
        ->create();

    expect($category->translations)->toHaveCount(2);
    expect($category->translations->pluck('locale')->toArray())->toContain('en', 'mk');
});

test('page factory can create translations', function () {
    $page = Page::factory()
        ->withTranslations(['en', 'de'])
        ->create();

    expect($page->translations)->toHaveCount(2);
    expect($page->translations->pluck('locale')->toArray())->toContain('en', 'de');
});

test('post factory can create translations', function () {
    $post = Post::factory()
        ->withTranslations(['en', 'mk', 'de', 'fr'])
        ->create();

    expect($post->translations)->toHaveCount(4);
    expect($post->translations->pluck('locale')->toArray())->toContain('en', 'mk', 'de', 'fr');
});

test('product factory creates translations with correct fields', function () {
    $product = Product::factory()
        ->withTranslations(['en', 'mk'])
        ->create();

    $enTranslation = $product->translation('en');
    $mkTranslation = $product->translation('mk');

    expect($enTranslation->name)->not->toBeNull();
    expect($enTranslation->summary)->not->toBeNull();
    expect($enTranslation->description)->not->toBeNull();
    expect($enTranslation->slug)->not->toBeNull();
    expect($enTranslation->meta_title)->not->toBeNull();
    expect($enTranslation->meta_description)->not->toBeNull();

    // Check that Macedonian translation has locale suffix
    expect($mkTranslation->name)->toContain('(MK)');
});

test('product factory withTranslationData creates specific translations', function () {
    $product = Product::factory()
        ->withTranslationData([
            'en' => [
                'name' => 'English Name',
                'summary' => 'English Summary',
            ],
            'mk' => [
                'name' => 'Македонско Име',
                'summary' => 'Македонски Резиме',
            ],
        ])
        ->create();

    expect($product->translation('en')->name)->toBe('English Name');
    expect($product->translation('mk')->name)->toBe('Македонско Име');
});

test('factory uses default locales when none specified', function () {
    $product = Product::factory()
        ->withTranslations()
        ->create();

    // Default locales are ['en', 'mk', 'de']
    expect($product->translations)->toHaveCount(3);
    expect($product->translations->pluck('locale')->toArray())->toContain('en', 'mk', 'de');
});
