<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTweet extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_show_user_tweets(): void
    {
        $tweet = Tweet::factory()->create();

        // \Log::debug($tweet);

        $response = $this->get('/api/user/tweets/{user_id}');

        $response->assertStatus(200);

        $response->assertJson([

            'id' => $tweet->id,

            'body' => $tweet->body,

            'user_id' =>$tweet->user_id,
        ]);
    }
}
