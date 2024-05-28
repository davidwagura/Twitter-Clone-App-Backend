<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function testCreateComment()
    {
        $data = [

            'body' => 'This a test comment',

            'user_id' => 4,

            'tweet_id' => 2

        ];

        $response = $this->postJson('/api/tweet/comment', $data);

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Comment created successfully',

            'comment' => [

                'body' => 'This a test comment',

                'user_id' => 4,
    
                'tweet_id' => 2
    
            ],

        ]);

     }
}
