<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_logout(): void
    {
        $response = $this->post('/api/logout');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'User logged out'

        ]);
    }
}
