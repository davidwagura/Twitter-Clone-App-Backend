<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TweetForYouTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_tweet_for_you(): void
    {
        $user = User::create([

            'id' => 2,

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

        ]);


        $tweet1 = Tweet::create([

            'id' => 1,

            'body' => 'Tweet one',

            'user_id' => $user->id
        ]);

        $tweet1 = Tweet::create([

            'id' => 2,

            'body' => 'Tweet two',

            'user_id' => $user->id
        ]);

        $response = $this->get('/api/for you');

        $response->assertStatus(200);

        $response->assertJson([
            
            'message' => 'Tweets displayed successfully'

        ]);

    }
    
}
