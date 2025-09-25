<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

test('user can register successfully', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->post('/register', $userData);

    $response->assertRedirect('/home');
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);
});

test('user can login successfully', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/home');
    $this->assertAuthenticatedAs($user);
});

test('user can logout successfully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/logout');

    $response->assertRedirect('/');
    $this->assertGuest();
});

test('user profile page loads', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/profile');

    $response->assertStatus(200);
    $response->assertSee($user->name);
});
