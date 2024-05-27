<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use App\Models\Comment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserCommentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_user_comments(): void
    {
        $user = User::create([

            'id' => 1,

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

        ]);


        $tweet = Tweet::create([

            'id' => 1,

            'body' => 'New tweet',

            'user_id' => 2

        ]);

        $comment1 = Comment::create([

            'id' => 1,

            'body' => 'First comment',

            'tweet_id' => $tweet->id,

            'user_id' => $user->id
        ]);

        $comment2 = Comment::create([

            'id' => 2,

            'body' => 'Second comment',

            'tweet_id' => $tweet->id,

            'user_id' => $user->id
        ]);

        $response = $this->get('/api/commented/comments/1');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'User comments displayed successfully'

        ]);

    }

}
