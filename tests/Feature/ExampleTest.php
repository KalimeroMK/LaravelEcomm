<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_basic_test()
    {
        // The root route '/' exists and returns 200
        $response = $this->get('/');

        if ($response->status() !== 200) {
            dump('Response status: '.$response->status());
            dump('Response content: '.$response->content());
        }

        $response->assertStatus(200);
    }
}
