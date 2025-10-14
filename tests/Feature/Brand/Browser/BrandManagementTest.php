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
    $response->assertSee('Brands');
});

test('admin can create brand', function () {
    $brandData = [
        'name' => 'Test Brand',
        'slug' => 'test-brand',
        'description' => 'Test Description',
        'logo' => 'test-logo.jpg',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/brands', $brandData);

    $response->assertRedirect();
    $this->assertDatabaseHas('brands', [
        'name' => 'Test Brand',
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
            'name' => 'Updated Brand',
            'slug' => 'updated-brand',
            'description' => 'Updated Description',
            'is_active' => true,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('brands', [
        'id' => $brand->id,
        'name' => 'Updated Brand',
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

    $response = $this->get("/brand/{$brand->slug}");

    $response->assertStatus(200);
    $response->assertSee($brand->name);
});
