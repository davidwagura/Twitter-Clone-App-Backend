<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a user.
     *
     * @return void
     */

    public function testCreateUser()
    {
        $data = [

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'john.doe@example.com',

            'username' => 'john',

            'password' => 'password123',

        ];

        $response = $this->postJson('/api/user', $data);

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'User created successfully',

            'user' => [

                'first_name' => 'John',

                'last_name' => 'Doe',

                'email' => 'john.doe@example.com',

                'username' => 'john',
            ],

        ]);

        $this->assertDatabaseHas('users', [

            'email' => 'john.doe@example.com',

            'username' => 'john',

        ]);

        $user = User::where('email', 'john.doe@example.com')->first();

        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
