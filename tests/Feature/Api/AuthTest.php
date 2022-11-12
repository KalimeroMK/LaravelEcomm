<?php

namespace Tests\Feature\Api;

use Modules\User\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * @test
     */
    public function testRegister()
    {
        $response = $this->json('POST', '/api/v1/register', [
            'name'             => 'Test',
            'email'            => time() . 'test@example.com',
            'password'         => '123456789',
            'confirm_password' => '123456789',
        
        ]);
        $response->assertStatus(200);
        // Receive our token
        $this->assertArrayHasKey('token', $response['data']);
    }
    
    /**
     * @test
     */
    public function testLogin()
    {
        User::create([
            'name'     => 'Test',
            'email'    => time() . 'example@example.com',
            'password' => bcrypt('123456789'),
        ]);
        
        $response = $this->json('POST', route('api.login'), [
            'email'    => time() . 'example@example.com',
            'password' => '123456789',
        ]);
        $response->assertStatus(200);
        
        $this->assertArrayHasKey('token', $response['data']);
    }
    
}