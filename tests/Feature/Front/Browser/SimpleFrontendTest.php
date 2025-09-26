<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('homepage loads successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('E-commerce Website');
});

test('about page loads', function () {
    $response = $this->get('/about-us');

    $response->assertStatus(200);
    $response->assertSee('About');
});

test('contact page loads', function () {
    $response = $this->get('/contact');

    $response->assertStatus(200);
    $response->assertSee('Contact');
});

test('product grids page loads', function () {
    $response = $this->get('/product-grids');

    $response->assertStatus(200);
    $response->assertSee('Products');
});

test('blog page loads', function () {
    $response = $this->get('/blog');

    $response->assertStatus(200);
    $response->assertSee('Blog');
});
