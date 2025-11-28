<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Category\Models\Category;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
});

test('admin can view categories list', function () {
    Category::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/categories');

    $response->assertStatus(200);
});

test('admin can create category', function () {
    $categoryData = [
        'title' => 'Test Category',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/categories', $categoryData);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'title' => 'Test Category',
    ]);
});

test('admin can edit category', function () {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->admin)
        ->get("/admin/categories/{$category->id}/edit");

    $response->assertStatus(200);
});

test('admin can update category', function () {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->admin)
        ->put("/admin/categories/{$category->id}", [
            'title' => 'Updated Category',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'title' => 'Updated Category',
    ]);
});

test('admin can delete category', function () {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete("/admin/categories/{$category->id}");

    $response->assertRedirect();
    // Category uses SoftDeletes, so check for deleted_at instead
    $category->refresh();
    expect($category->deleted_at)->not->toBeNull();
});

test('category page displays products', function () {
    $category = Category::factory()->create();

    $response = $this->get(route('front.product-cat', $category->slug));

    $response->assertStatus(200);
});
