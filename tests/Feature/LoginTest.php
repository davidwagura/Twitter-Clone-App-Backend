<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_login(): void
    {
        $user = User::create([

            'id' => 1,

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'John1234'
        ]);

        $data = [

            'email' => $user->email,

            'password' => $user->password
        ];

        $response = $this->post('/api/login', $data);

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Login successful'

        ]);
    }
}
