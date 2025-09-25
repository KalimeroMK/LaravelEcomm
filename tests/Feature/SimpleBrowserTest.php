<?php

declare(strict_types=1);

test('homepage loads successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Laravel');
});

test('admin login page loads', function () {
    $response = $this->get('/admin/login');

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
    $response = $this->get('/products');

    $response->assertStatus(200);
});

test('categories page loads', function () {
    $response = $this->get('/categories');

    $response->assertStatus(200);
});

test('brands page loads', function () {
    $response = $this->get('/brands');

    $response->assertStatus(200);
});

test('about page loads', function () {
    $response = $this->get('/about');

    $response->assertStatus(200);
});

test('contact page loads', function () {
    $response = $this->get('/contact');

    $response->assertStatus(200);
});

test('newsletter subscription page loads', function () {
    $response = $this->get('/newsletter');

    $response->assertStatus(200);
});
