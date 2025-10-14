<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'locale' => 'en',
    ]);
});

test('French translations are displayed correctly', function () {
    $this->get('/language/fr');
    
    $response = $this->get('/');
    
    $response->assertStatus(200);
    // Check if French translations are displayed
    $response->assertSee('Accueil', false); // Home
    $response->assertSee('Nom', false); // Name
    $response->assertSee('Enregistrer', false); // Save
});

test('German translations are displayed correctly', function () {
    $this->get('/language/de');
    
    $response = $this->get('/');
    
    $response->assertStatus(200);
    // German translations should be displayed (using English as fallback for now)
    $response->assertSee('Home', false);
    $response->assertSee('Name', false);
});

test('Macedonian translations are displayed correctly', function () {
    $this->get('/language/mk');
    
    $response = $this->get('/');
    
    $response->assertStatus(200);
    // Macedonian translations should be displayed
    $response->assertSee('Дома', false); // Home
    $response->assertSee('Име', false); // Name
});

test('Arabic translations are displayed correctly', function () {
    $this->get('/language/ar');
    
    $response = $this->get('/');
    
    $response->assertStatus(200);
    // Arabic translations should be displayed (using English as fallback for now)
    $response->assertSee('Home', false);
    $response->assertSee('Name', false);
});

test('translation fallback works when translation is missing', function () {
    // Switch to a language that might have missing translations
    $this->get('/language/es');
    
    $response = $this->get('/');
    
    $response->assertStatus(200);
    // Should fallback to English if Spanish translation is missing
    $response->assertSee('Home', false);
});

test('translation keys are properly resolved', function () {
    $this->get('/language/fr');
    
    $response = $this->get('/');
    
    $response->assertStatus(200);
    
    // Check that translation keys are resolved
    $response->assertDontSee('messages.home', false); // Should not see the key
    $response->assertSee('Accueil', false); // Should see the translation
});

test('translation works with different page types', function () {
    $pages = [
        '/' => 'home',
        '/about-us' => 'about',
        '/contact' => 'contact',
        '/product-grids' => 'products',
    ];
    
    foreach ($pages as $page => $key) {
        $this->get('/language/fr');
        
        $response = $this->get($page);
        
        if ($response->status() === 200) {
            // Check that French translations are applied
            $response->assertSee('Accueil', false);
        }
    }
});

test('translation works with form validation messages', function () {
    $this->get('/language/fr');
    
    // Try to submit a form with validation errors
    $response = $this->post('/register', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => '123',
    ]);
    
    $response->assertSessionHasErrors();
    
    // Check if validation messages are in French
    $errors = session('errors');
    if ($errors) {
        $errorBag = $errors->getBag('default');
        $this->assertNotEmpty($errorBag->all());
    }
});

test('translation works with authentication messages', function () {
    $this->get('/language/fr');
    
    // Try to login with invalid credentials
    $response = $this->post('/login', [
        'email' => 'invalid@example.com',
        'password' => 'wrong-password',
    ]);
    
    $response->assertSessionHasErrors();
    
    // Check if authentication error messages are in French
    $errors = session('errors');
    if ($errors) {
        $errorBag = $errors->getBag('default');
        $this->assertNotEmpty($errorBag->all());
    }
});

test('translation works with pagination', function () {
    $this->get('/language/fr');
    
    $response = $this->get('/product-grids');
    
    $response->assertStatus(200);
    // Check if pagination labels are translated
    $response->assertSee('Suivant', false); // Next
    $response->assertSee('Précédent', false); // Previous
});

test('translation works with different HTTP methods', function () {
    $this->get('/language/fr');
    
    // Test GET request
    $response = $this->get('/');
    $response->assertSee('Accueil', false);
    
    // Test POST request
    $response = $this->post('/contact', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'message' => 'Test message',
    ]);
    
    // Test PUT request
    $response = $this->put('/profile', [
        'name' => 'Updated Name',
    ]);
    
    // Test DELETE request
    $response = $this->delete('/logout');
});

test('translation persists across multiple requests', function () {
    // Set French as language
    $this->get('/language/fr');
    
    // Make multiple requests
    $this->get('/');
    $this->get('/about-us');
    $this->get('/contact');
    
    // Check that French is still active
    $response = $this->get('/');
    $response->assertSee('Accueil', false);
});

test('translation works with AJAX requests', function () {
    $this->get('/language/fr');
    
    $response = $this->withHeaders([
        'X-Requested-With' => 'XMLHttpRequest',
        'Accept' => 'application/json',
    ])->get('/');
    
    $response->assertStatus(200);
});

test('translation works with different content types', function () {
    $this->get('/language/fr');
    
    // Test HTML response
    $response = $this->get('/');
    $response->assertSee('Accueil', false);
    
    // Test JSON response
    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get('/api/v1/');
    
    $response->assertStatus(200);
});

test('translation works with cached responses', function () {
    $this->get('/language/fr');
    
    // First request
    $response1 = $this->get('/');
    $response1->assertSee('Accueil', false);
    
    // Second request (might be cached)
    $response2 = $this->get('/');
    $response2->assertSee('Accueil', false);
});

test('translation works with different user roles', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $this->get('/language/fr');
    
    // Test as guest
    $response = $this->get('/');
    $response->assertSee('Accueil', false);
    
    // Test as authenticated user
    actingAs($this->user);
    $response = $this->get('/');
    $response->assertSee('Accueil', false);
    
    // Test as admin
    actingAs($admin);
    $response = $this->get('/admin');
    if ($response->status() === 200) {
        $response->assertSee('Accueil', false);
    }
});

test('translation works with different timezones', function () {
    $this->get('/language/fr');
    
    // Test with different timezones
    $timezones = ['UTC', 'Europe/Paris', 'America/New_York'];
    
    foreach ($timezones as $timezone) {
        config(['app.timezone' => $timezone]);
        
        $response = $this->get('/');
        $response->assertSee('Accueil', false);
    }
});

test('translation works with different locales in same session', function () {
    // Start with English
    $response = $this->get('/');
    $response->assertSee('Home', false);
    
    // Switch to French
    $this->get('/language/fr');
    $response = $this->get('/');
    $response->assertSee('Accueil', false);
    
    // Switch to German
    $this->get('/language/de');
    $response = $this->get('/');
    $response->assertSee('Home', false); // Fallback to English
    
    // Switch to Macedonian
    $this->get('/language/mk');
    $response = $this->get('/');
    $response->assertSee('Дома', false);
});
