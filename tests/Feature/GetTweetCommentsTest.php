<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tweet;
use App\Models\Comment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetTweetCommentsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_tweet_comments(): void
    {
        $tweet = Tweet::create([
            'id' => 1,

            'body' => 'New tweet',

            'user_id' => 2

        ]);

        $comment1 = Comment::create([
            'id' => 1,

            'body' => 'First comment',

            'tweet_id' => $tweet->id,

            'user_id' => $tweet->user_id
        ]);

        $comment2 = Comment::create([
            'id' => 2,

            'body' => 'Second comment',

            'tweet_id' => $tweet->id,

            'user_id' => $tweet->id
        ]);

        $response = $this->get('/api/comments/1');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Comments displayed successfully',

            // 'comment' => $comment1, $comment2
        ]);
        
    }
}
