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
    $response->assertSee('Categories');
});

test('admin can create category', function () {
    $categoryData = [
        'name' => 'Test Category',
        'slug' => 'test-category',
        'description' => 'Test Description',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/categories', $categoryData);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);
});

test('admin can edit category', function () {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->admin)
        ->get("/admin/categories/{$category->id}/edit");

    $response->assertStatus(200);
    $response->assertSee($category->name);
});

test('admin can update category', function () {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->admin)
        ->put("/admin/categories/{$category->id}", [
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'description' => 'Updated Description',
            'is_active' => true,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Category',
    ]);
});

test('admin can delete category', function () {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete("/admin/categories/{$category->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});

test('category page displays products', function () {
    $category = Category::factory()->create();

    $response = $this->get("/category/{$category->slug}");

    $response->assertStatus(200);
    $response->assertSee($category->name);
});
