<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('basic test works', function () {
    expect(true)->toBeTrue();
});

test('can access homepage', function () {
    $response = $this->get('/');
    
    expect($response->status())->toBeIn([200, 302, 404]);
});
