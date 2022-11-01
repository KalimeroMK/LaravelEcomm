<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CoreTest extends TestCase
{
    public $model;
    
    protected function authenticate(): mixed
    {
        if (Auth::attempt(['email' => 'superadmin@mail.com', 'password' => 'password'])) {
            return auth()->user()->createToken('authToken')->accessToken;
        }
        
        return 'wrong credentials';
    }
    
    /**
     * Make paths for storing images.
     *
     * @return object
     */
    public function makePaths(): object
    {
        $original  = public_path() . '/uploads/images/';
        $thumbnail = public_path() . '/uploads/images/thumbnails/';
        $medium    = public_path() . '/uploads/images/medium/';
        
        return (object)compact('original', 'thumbnail', 'medium');
    }
    
    /**
     * @param  string  $url
     * @param  string  $token
     *
     * @return TestResponse
     */
    public function list(string $url, string $token): TestResponse
    {
        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $token,
            ]
        )->json('GET', $url);
        
        Log::info(1, [$response->getContent()]);
        
        return $response->assertStatus(200);
    }
    
    /**
     * @param  string  $url
     * @param  string  $token
     * @param  array  $data
     *
     * @return TestResponse
     */
    public function create(string $url, string $token, array $data): TestResponse
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->json(
            'POST',
            $url,
            $data
        );
        Log::info(1, [$response->getContent()]);
        
        return $response->assertStatus(200);
    }
    
    /**
     * @param  string  $url
     * @param  string  $token
     * @param  array  $data
     * @param  int  $id
     *
     * @return TestResponse
     */
    public function update(string $url, string $token, array $data, int $id): TestResponse
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->json(
            'POST',
            $url . $id,
            $data
        );
        Log::info(1, [$response->getContent()]);
        
        return $response->assertStatus(200);
    }
    
    /**
     * @param  string  $url
     * @param  string  $token
     * @param  array  $data
     * @param  int  $id
     *
     * @return TestResponse
     */
    public function updatePUT(string $url, string $token, array $data, int $id): TestResponse
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->json(
            'PUT',
            $url . $id,
            $data
        );
        Log::info(1, [$response->getContent()]);
        
        return $response->assertStatus(200);
    }
    
    /**
     * @param  string  $url
     * @param  string  $token
     * @param  int  $id
     *
     * @return TestResponse
     */
    public function show(string $url, string $token, int $id): TestResponse
    {
        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $token,
            ]
        )->json('GET', $url . $id);
        
        Log::info(1, [$response->getContent()]);
        
        return $response->assertStatus(200);
    }
    
    /**
     * @param  string  $url
     * @param  string  $token
     * @param  int  $id
     *
     * @return TestResponse
     */
    public function destroy(string $url, string $token, int $id): TestResponse
    {
        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $token,
            ]
        )->json('DELETE', $url . $id);
        
        Log::info(1, [$response->getContent()]);
        
        return $response->assertStatus(200);
    }
    
}