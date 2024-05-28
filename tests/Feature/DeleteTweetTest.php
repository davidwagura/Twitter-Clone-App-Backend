<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTweetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_delete_tweet(): void
    {
        $tweet = Tweet::create([

            'body' => 'Tweet created successfully',

            'user_id' => 1
        ]);
        
        $response = $this->delete('/api/tweet/delete/1');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Tweet deleted successfully'

        ]);
    }
}
