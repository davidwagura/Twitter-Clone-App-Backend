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
        // Prepare the data for creating a user
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'username' => 'johndoe',
            'password' => 'password123',
        ];

        // Send a POST request to the /api/user route
        $response = $this->postJson('/api/user', $data);

        // Assert that the response status is 200
        $response->assertStatus(200);

        // Assert that the response JSON contains the expected data
        $response->assertJson([
            'message' => 'User created successfully',
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'username' => 'johndoe',
            ],
        ]);

        // Check that the user was inserted into the database
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'username' => 'johndoe',
        ]);

        // Verify that the password was hashed correctly
        $user = User::where('email', 'john.doe@example.com')->first();

        dd($user);

        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
