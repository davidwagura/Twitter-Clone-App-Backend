<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_user_profile(): void
    {
        $user = User::create([

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234'

        ]);

        $tweet1 = Tweet::create([
            
            'body' => 'My first tweet',

            'user_id' => $user->id
        ]);


        $tweet2 = Tweet::create([

            'body' => 'This is my second tweet',

            'user_id' => $user->id
        ]);

        $tweet3 = Tweet::create([

            'body' => 'This is my third tweet',

            'user_id' => $user->id
        ]);
        
        $response = $this->get('/api/profile/1');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Profile displayed successfully',

        ]);

        $response->assertJsonCount(3, 'tweets.0.tweet');

    }
}
