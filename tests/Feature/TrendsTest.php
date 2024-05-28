<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tweet;
use App\Models\Comment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrendsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_trends(): void
    {       
    $tweet = Tweet::create([
        
        'body' => 'New tweet',

        'user_id' => 2

    ]);

    $comment1 = Comment::create([

        'body' => 'First comment',

        'tweet_id' => $tweet->id,

        'user_id' => $tweet->user_id
    ]);

    $comment2 = Comment::create([

        'body' => 'Second comment',

        'tweet_id' => $tweet->id,

        'user_id' => $tweet->id
    ]);

    $tweet1 = Tweet::create([

        'body' => 'This is a tweet',

        'user_id' => 5,
    ]);

    $comment2 = Comment::create([

        'body' => 'Second comment',

        'tweet_id' => $tweet1->id,

        'user_id' => 3
    ]);


        $response = $this->get('/api/trends');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Trending tweets displayed successfully'

        ]);

    }
    
}
