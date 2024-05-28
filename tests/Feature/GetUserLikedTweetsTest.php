<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetUserLikedTweetsTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic feature test example.
     */
    public function test_get_user_liked_tweets(): void
    {
        $like = User::create([

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',


        ]);

        $user = User::create([

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',


        ]);

        $tweet = Tweet::create([

            'id' => 2,

            'body' => 'This is a tweet',

            'user_id' => $user->id,

            'likes_id' => $like->id

        ]);
        
        $response = $this->get('/api/userLikedTweets/1');

        $response->assertStatus(200);

    }
    
}
