<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('user can enable 2FA', function () {
    $response = $this->actingAs($this->user)
        ->get('/2fa/enable');
    
    $response->assertStatus(200);
    $response->assertSee('Enable Two-Factor Authentication');
});

test('user can generate 2FA secret', function () {
    $response = $this->actingAs($this->user)
        ->post('/2fa/generate-secret');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'secret',
        'qr_code'
    ]);
});

test('user can verify 2FA setup', function () {
    $secret = 'TEST_SECRET_KEY';
    
    $response = $this->actingAs($this->user)
        ->post('/2fa/verify-setup', [
            'secret' => $secret,
            'code' => '123456'
        ]);
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'message'
    ]);
});

test('user can disable 2FA', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);
    
    $response = $this->actingAs($this->user)
        ->post('/2fa/disable', [
            'password' => 'password'
        ]);
    
    $response->assertRedirect();
    $this->user->refresh();
    expect($this->user->google2fa_secret)->toBeNull();
});

test('user with 2FA enabled must provide code on login', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);
    
    $response = $this->post('/login', [
        'email' => $this->user->email,
        'password' => 'password'
    ]);
    
    $response->assertRedirect('/2fa/verify');
});

test('user can verify 2FA code during login', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);
    
    $response = $this->post('/2fa/verify', [
        'code' => '123456'
    ]);
    
    $response->assertRedirect('/home');
    $this->assertAuthenticatedAs($this->user);
});

test('invalid 2FA code is rejected', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);
    
    $response = $this->post('/2fa/verify', [
        'code' => '000000'
    ]);
    
    $response->assertSessionHasErrors(['code']);
});

test('user can view 2FA recovery codes', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);
    
    $response = $this->actingAs($this->user)
        ->get('/2fa/recovery-codes');
    
    $response->assertStatus(200);
    $response->assertSee('Recovery Codes');
});

test('user can generate new recovery codes', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);
    
    $response = $this->actingAs($this->user)
        ->post('/2fa/recovery-codes/regenerate');
    
    $response->assertRedirect();
    $response->assertSessionHas('recovery_codes');
});

test('user can use recovery code for login', function () {
    $this->user->update([
        'google2fa_secret' => 'TEST_SECRET',
        'recovery_codes' => ['recovery123', 'recovery456']
    ]);
    
    $response = $this->post('/2fa/recovery', [
        'recovery_code' => 'recovery123'
    ]);
    
    $response->assertRedirect('/home');
    $this->assertAuthenticatedAs($this->user);
});

test('used recovery code cannot be reused', function () {
    $this->user->update([
        'google2fa_secret' => 'TEST_SECRET',
        'recovery_codes' => ['recovery123']
    ]);
    
    // First use
    $this->post('/2fa/recovery', [
        'recovery_code' => 'recovery123'
    ]);
    
    // Second use should fail
    $response = $this->post('/2fa/recovery', [
        'recovery_code' => 'recovery123'
    ]);
    
    $response->assertSessionHasErrors(['recovery_code']);
});

test('admin can view 2FA settings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->get('/admin/2fa/settings');
    
    $response->assertStatus(200);
    $response->assertSee('Two-Factor Authentication Settings');
});

test('admin can enforce 2FA for users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/2fa/enforce', [
            'user_id' => $this->user->id
        ]);
    
    $response->assertRedirect();
    $this->user->refresh();
    expect($this->user->force_2fa)->toBeTrue();
});
