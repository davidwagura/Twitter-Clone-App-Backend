<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateRetweetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_retweet(): void
    {
        $user = User::create([

            'id' => 1,

            'first_name' => 'James',

            'last_name' => 'Peter',

            'email' => 'James@gmail.com',

            'username' => 'James',

            'password' => 'James1234'

        ]);

        $tweet = Tweet::create([

            'id' => 1,

            'body' => 'A new tweet it is.',

            'user_id' => $user->id
        ]);
        
        $response = $this->get('/api/retweet/{tweet_id}/{user_id}');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Retweet successful'

        ]);
    }
}
