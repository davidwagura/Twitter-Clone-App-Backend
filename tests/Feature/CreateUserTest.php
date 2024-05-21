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
            'username' => 'johndoe',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/user', $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'User created successfully',
                     'user' => [
                         'first_name' => 'John',
                         'last_name' => 'Doe',
                         'email' => 'john.doe@example.com',
                         'username' => 'johndoe',
                     ],
                 ]);

        // Ensure the user was actually created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'username' => 'johndoe',
        ]);

        // Ensure the password is hashed
        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
