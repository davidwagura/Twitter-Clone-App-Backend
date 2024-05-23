<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeTweetTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_like_tweet(): void
    {   
        $user = User::create([
            'id' => 5,

            'first_name' => 'John',

            'last_name' => 'White',

            'email' => 'John@gmail.com',

            'username' => 'Johnty',

            'password' => 'johnwhite456'

        ]);
        
        $tweet = Tweet::create([

            'id' => 1,

            'body' => 'Thi is my first tweet',

            'user-id' => 5

        ]);

        $response = $this->post('/api/like/{tweet_id}/{user_id}');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Tweet liked successfully',

            // 'tweet' => $tweet

        ]);
    }
}
