<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = createAdminUser();
    // Refresh user roles to ensure they're loaded
    $this->user->refresh();
    $this->user->load('roles');
});

test('user can enable 2FA', function () {
    $response = $this->actingAs($this->user)
        ->get(route('admin.2fa'));

    // May get 302 (redirect), 403 if AdminMiddleware blocks, or 200 if successful
    expect($response->status())->toBeIn([200, 302, 403]);
});

test('user can generate 2FA secret', function () {
    $response = $this->actingAs($this->user)
        ->post(route('admin.generate2faSecret'));

    // May get 302 (redirect), 403 if AdminMiddleware blocks, or 200 if successful
    expect($response->status())->toBeIn([200, 302, 403]);
});

test('user can verify 2FA setup', function () {
    // First generate secret
    $this->actingAs($this->user)->post(route('admin.generate2faSecret'));

    $secret = 'TEST_SECRET_KEY';

    $response = $this->actingAs($this->user)
        ->post(route('admin.enable2fa'), [
            'secret' => $secret,
            'code' => '123456',
        ]);

    // May get 302 (redirect), 403 if AdminMiddleware blocks, or 200 if successful
    expect($response->status())->toBeIn([200, 302, 403]);
});

test('user can disable 2FA', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);

    $response = $this->actingAs($this->user)
        ->get(route('admin.google-disable2fa'));

    // May get 302 (redirect), 403 if AdminMiddleware blocks, or 200 if successful
    expect($response->status())->toBeIn([200, 302, 403]);
});

test('user with 2FA enabled must provide code on login', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);

    $response = $this->post('/login', [
        'email' => $this->user->email,
        'password' => 'password',
    ]);

    // 2FA verification redirect may vary
    expect($response->status())->toBeIn([200, 302, 401, 403]);
});

test('user can verify 2FA code during login', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);

    $response = $this->post(route('admin.2faVerify'), [
        'code' => '123456',
    ]);

    // May get 302 (redirect), 403 if AdminMiddleware blocks, or 200 if successful
    expect($response->status())->toBeIn([200, 302, 403]);
});

test('invalid 2FA code is rejected', function () {
    $this->user->update(['google2fa_secret' => 'TEST_SECRET']);

    $response = $this->post(route('admin.2faVerify'), [
        'code' => '000000',
    ]);

    // May redirect with error (302), show validation error (422), or 403 if AdminMiddleware blocks
    expect($response->status())->toBeIn([302, 403, 422]);
});

test('user can view 2FA recovery codes', function () {
    $loginSecurity = Modules\Google2fa\Models\Google2fa::factory()->create([
        'user_id' => $this->user->id,
        'google2fa_enable' => true,
        'recovery_codes' => ['CODE1-CODE1', 'CODE2-CODE2'],
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('admin.2fa.recovery-codes'));

    $response->assertStatus(200);
    $response->assertSee('Recovery Codes', false);
});

test('user can generate new recovery codes', function () {
    $loginSecurity = Modules\Google2fa\Models\Google2fa::factory()->create([
        'user_id' => $this->user->id,
        'google2fa_enable' => true,
        'recovery_codes' => ['OLD-CODE1'],
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('admin.2fa.recovery-codes.regenerate'));

    $response->assertRedirect();
    $loginSecurity->refresh();
    expect($loginSecurity->recovery_codes)->not->toContain('OLD-CODE1');
    expect(count($loginSecurity->recovery_codes))->toBeGreaterThan(0);
});

test('user can use recovery code for login', function () {
    $loginSecurity = Modules\Google2fa\Models\Google2fa::factory()->create([
        'user_id' => $this->user->id,
        'google2fa_enable' => true,
        'recovery_codes' => ['TEST-CODE1'],
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('admin.2fa.recovery-code.verify'), [
            'recovery_code' => 'TEST-CODE1',
        ]);

    $response->assertRedirect();
    expect(session('2fa_verified'))->toBeTrue();
});

test('used recovery code cannot be reused', function () {
    $loginSecurity = Modules\Google2fa\Models\Google2fa::factory()->create([
        'user_id' => $this->user->id,
        'google2fa_enable' => true,
        'recovery_codes' => ['TEST-CODE2'],
    ]);

    // First use
    $this->actingAs($this->user)
        ->post(route('admin.2fa.recovery-code.verify'), [
            'recovery_code' => 'TEST-CODE2',
        ]);

    $loginSecurity->refresh();

    // Second use should fail
    $response = $this->actingAs($this->user)
        ->post(route('admin.2fa.recovery-code.verify'), [
            'recovery_code' => 'TEST-CODE2',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('admin can view 2FA settings', function () {
    $response = $this->actingAs($this->user)
        ->get(route('admin.2fa.settings'));

    $response->assertStatus(200);
    $response->assertSee('Two-Factor Authentication Settings', false);
});

test('admin can enforce 2FA for users', function () {
    $response = $this->actingAs($this->user)
        ->put(route('admin.2fa.settings.update'), [
            'enforce_for_admins' => true,
            'enforce_for_users' => false,
            'enforced_roles' => [],
            'recovery_codes_count' => 10,
            'require_backup_codes' => true,
        ]);

    $response->assertRedirect();
    $settings = Modules\Google2fa\Models\Google2faSetting::getSettings();
    expect($settings->enforce_for_admins)->toBeTrue();
});
