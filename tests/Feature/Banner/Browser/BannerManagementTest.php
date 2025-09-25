<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use Modules\Banner\Models\Banner;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('admin can view banners list', function () {
    Banner::factory()->count(5)->create();
    
    $response = $this->actingAs($this->admin)
        ->get('/admin/banners');
    
    $response->assertStatus(200);
    $response->assertSee('Banners');
});

test('admin can create banner', function () {
    $bannerData = [
        'title' => 'Summer Sale',
        'subtitle' => 'Up to 50% off',
        'image' => 'banner-summer.jpg',
        'link' => '/sale',
        'position' => 'homepage-top',
        'is_active' => true,
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addMonth()->format('Y-m-d')
    ];
    
    $response = $this->actingAs($this->admin)
        ->post('/admin/banners', $bannerData);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('banners', [
        'title' => 'Summer Sale',
        'position' => 'homepage-top'
    ]);
});

test('admin can edit banner', function () {
    $banner = Banner::factory()->create();
    
    $response = $this->actingAs($this->admin)
        ->get("/admin/banners/{$banner->id}/edit");
    
    $response->assertStatus(200);
    $response->assertSee($banner->title);
});

test('admin can update banner', function () {
    $banner = Banner::factory()->create([
        'title' => 'Old Title'
    ]);
    
    $response = $this->actingAs($this->admin)
        ->put("/admin/banners/{$banner->id}", [
            'title' => 'New Title',
            'subtitle' => 'New Subtitle',
            'is_active' => true
        ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('banners', [
        'id' => $banner->id,
        'title' => 'New Title',
        'subtitle' => 'New Subtitle'
    ]);
});

test('admin can delete banner', function () {
    $banner = Banner::factory()->create();
    
    $response = $this->actingAs($this->admin)
        ->delete("/admin/banners/{$banner->id}");
    
    $response->assertRedirect();
    $this->assertDatabaseMissing('banners', [
        'id' => $banner->id
    ]);
});

test('active banners display on homepage', function () {
    Banner::factory()->create([
        'title' => 'Active Banner',
        'position' => 'homepage-top',
        'is_active' => true,
        'start_date' => now()->subDay(),
        'end_date' => now()->addMonth()
    ]);
    
    Banner::factory()->create([
        'title' => 'Inactive Banner',
        'position' => 'homepage-top',
        'is_active' => false
    ]);
    
    $response = $this->get('/');
    
    $response->assertStatus(200);
    $response->assertSee('Active Banner');
    $response->assertDontSee('Inactive Banner');
});

test('banners respect date range', function () {
    Banner::factory()->create([
        'title' => 'Future Banner',
        'position' => 'homepage-top',
        'is_active' => true,
        'start_date' => now()->addDay(),
        'end_date' => now()->addMonth()
    ]);
    
    Banner::factory()->create([
        'title' => 'Expired Banner',
        'position' => 'homepage-top',
        'is_active' => true,
        'start_date' => now()->subMonth(),
        'end_date' => now()->subDay()
    ]);
    
    $response = $this->get('/');
    
    $response->assertStatus(200);
    $response->assertDontSee('Future Banner');
    $response->assertDontSee('Expired Banner');
});

test('banners display in correct positions', function () {
    Banner::factory()->create([
        'title' => 'Top Banner',
        'position' => 'homepage-top',
        'is_active' => true
    ]);
    
    Banner::factory()->create([
        'title' => 'Sidebar Banner',
        'position' => 'sidebar',
        'is_active' => true
    ]);
    
    $response = $this->get('/');
    
    $response->assertStatus(200);
    $response->assertSee('Top Banner');
    $response->assertSee('Sidebar Banner');
});

test('admin can reorder banners', function () {
    $banner1 = Banner::factory()->create(['sort_order' => 1]);
    $banner2 = Banner::factory()->create(['sort_order' => 2]);
    
    $response = $this->actingAs($this->admin)
        ->post('/admin/banners/reorder', [
            'banner_ids' => [$banner2->id, $banner1->id]
        ]);
    
    $response->assertRedirect();
    
    $banner1->refresh();
    $banner2->refresh();
    
    expect($banner2->sort_order)->toBe(1);
    expect($banner1->sort_order)->toBe(2);
});
