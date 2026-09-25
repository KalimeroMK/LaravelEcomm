<?php

declare(strict_types=1);

test('homepage loads successfully', function () {
    $this->get('/')->assertRedirect();

    $response = $this->get('/en');

    $response->assertStatus(200);
    // Just verify page loads - don't check for specific text as it depends on theme
});

test('admin login page loads', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('user login page loads', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('user register page loads', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('products page loads', function () {
    $response = $this->get(route('front.product-grids'));

    $response->assertStatus(200);
});

test('about page loads', function () {
    $response = $this->get('/en/about-us');

    $response->assertStatus(200);
});

test('contact page loads', function () {
    $response = $this->get('/en/contact');

    $response->assertStatus(200);
});
