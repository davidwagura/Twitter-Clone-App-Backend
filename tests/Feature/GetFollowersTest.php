<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetFollowersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_get_user_followers(): void
    {
        $follower = User::create([

            'first_name' => 'Mary',

            'last_name' => 'Johnson',

            'email' => 'Mary@gmail.com',

            'username' => 'Mary',

            'password' => 'mj123456',

        ]);

        $user = User::create([

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

            'followers_id' => $follower->id
            
        ]);

        
        $response = $this->get('/api/myFollowers/1');

        $response->assertStatus(200);

        // $response->assertJsonFragment([

        //     'first_name' => 'Kings'

        // ]);

    }

}
