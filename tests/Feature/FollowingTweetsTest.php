<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FollowingTweetsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_following_tweets(): void
    {
        $user1 = User::create([

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

        ]);

        $user2 = User::create([

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

            'followings_id' => $user1->id

        ]);

        $tweet = Tweet::create([

            'body' => 'This is a tweet',

            'user_id' => $user2->id
            
        ]);


        $response = $this->get('/api/followingTweets/2');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Displaying tweets'

        ]);

    }
}
