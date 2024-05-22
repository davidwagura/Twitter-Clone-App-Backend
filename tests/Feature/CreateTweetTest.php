<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTweetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

     public function testCreateTweet()
     {

        $data = [

            'body' => 'New testing tweet',

            'user_id' => 2,

        ];

        $response = $this->postJson('/api/tweet', $data);

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Tweet created successfully',

            'tweet' => [

                'body' => 'New testing tweet',

                'user_id' => 2,

            ],

        ]);

        $tweet = Tweet::where('user_id', 2)->first();

        // dd($tweet);

     }
}

