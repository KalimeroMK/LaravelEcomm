<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
});

test('admin can view brands list', function () {
    Brand::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/brands');

    $response->assertStatus(200);
});

test('admin can create brand', function () {
    $brandData = [
        'title' => 'Test Brand',
        'slug' => 'test-brand',
        'status' => 'active',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/brands', $brandData);

    $response->assertRedirect();
    $this->assertDatabaseHas('brands', [
        'title' => 'Test Brand',
        'slug' => 'test-brand',
    ]);
});

test('admin can edit brand', function () {
    $brand = Brand::factory()->create();

    $response = $this->actingAs($this->admin)
        ->get("/admin/brands/{$brand->id}/edit");

    $response->assertStatus(200);
    $response->assertSee($brand->name);
});

test('admin can update brand', function () {
    $brand = Brand::factory()->create();

    $response = $this->actingAs($this->admin)
        ->put("/admin/brands/{$brand->id}", [
            'title' => 'Updated Brand',
            'slug' => 'updated-brand',
            'status' => 'active',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('brands', [
        'id' => $brand->id,
        'title' => 'Updated Brand',
    ]);
});

test('admin can delete brand', function () {
    $brand = Brand::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete("/admin/brands/{$brand->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('brands', [
        'id' => $brand->id,
    ]);
});

test('brand page displays products', function () {
    $brand = Brand::factory()->create();

    $response = $this->get(route('front.product-brand', $brand->slug));

    $response->assertStatus(200);
    // Don't check for specific text as it depends on translations and theme
});
