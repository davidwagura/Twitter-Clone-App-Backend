<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Follower;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserFollowerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_follower(): void
    {
        $user = User::create([
            'id' => 1,

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'john.doe1@example.com',

            'username' => 'johnDoe',

            'password' => 'password123',       
        ]);
        $follower1 = User::create([
            'id' => 1,

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'john.doe2@example.com',

            'username' => 'johnDoe',

            'password' => 'password123',       
        ]);
        $follower2 = User::create([
            'id' => 2,

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'john.doe3@example.com',

            'username' => 'johnDoe',

            'password' => 'password123',       
        ]);

        $response = $this->get('/api/followers/1/2');

        $response->assertStatus(200);
    }
}
