<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('homepage loads successfully', function () {
    // The bare root redirects to the default locale.
    $this->get('/')->assertRedirect();

    $response = $this->get('/en');

    $response->assertStatus(200);
    $response->assertSee('Hot Item');
});

test('about page loads', function () {
    $response = $this->get('/en/about-us');

    $response->assertStatus(200);
    $response->assertSee('About');
});

test('contact page loads', function () {
    $response = $this->get('/en/contact');

    $response->assertStatus(200);
    $response->assertSee('Contact');
});

test('product grids page loads', function () {
    $response = $this->get('/en/product-grids');

    $response->assertStatus(200);
    $response->assertSee('Products');
});

test('blog page loads', function () {
    $response = $this->get('/en/blog');

    $response->assertStatus(200);
    $response->assertSee('Blog');
});
