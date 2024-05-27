<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetFollowingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_get_users_following(): void
    {
        $follower = User::create([

            'id' => 1,

            'first_name' => 'Mary',

            'last_name' => 'Johnson',

            'email' => 'Mary@gmail.com',

            'username' => 'Mary',

            'password' => 'mj123456',

        ]);

        $user = User::create([

            'id' => 1,

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

            'followings_id' => $follower->id
            
        ]);

        $response = $this->get('/api/showFollowing/1');

        $response->assertStatus(200);

        $response->assertJsonFragment([

            'id' => 1,

            'first_name' => 'Mary'

        ]);

    }
}
