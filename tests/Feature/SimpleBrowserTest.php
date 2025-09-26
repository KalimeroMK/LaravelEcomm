<?php

declare(strict_types=1);

test('homepage loads successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Laravel');
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
    $response = $this->get('/product-grids');

    $response->assertStatus(200);
});


test('about page loads', function () {
    $response = $this->get('/about-us');

    $response->assertStatus(200);
});

test('contact page loads', function () {
    $response = $this->get('/contact');

    $response->assertStatus(200);
});

