<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserFollowingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_user_following(): void
    {
        $user = User::create([

            'first_name' => 'Ken',

            'last_name' => 'Johnson',

            'email' => 'Ken@gmail.com',

            'username' => 'Johnson',

            'password' => 'Ken12345'

        ]);

        $following = User::create([

            'first_name' => 'Moses',

            'last_name' => 'White',

            'email' => 'Moses@gmail.com',

            'username' => 'Moses',

            'password' => 'White1234'
        ]);
        
        $response = $this->post('/api/following/1/1');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Followed successfully'

        ]);

        $response->assertJsonFragment([

            'username' => 'Johnson'
        ]);

    }

}
