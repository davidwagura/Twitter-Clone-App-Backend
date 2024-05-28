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

            'first_name' => 'John',

            'last_name' => 'White',

            'email' => 'John@gmail.com',

            'username' => 'Johnty',

            'password' => 'johnwhite456'

        ]);
        
        $tweet = Tweet::create([

            'body' => 'Thi is my first tweet',

            'user_id' => 1

        ]);

        $response = $this->post('/api/like/1/1');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Tweet liked successfully',

        ]);

    }
    
}
