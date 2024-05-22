<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTweetTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function testGetAllUserTweets(): void
    {
        $user = User::create([

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'john.doe@example.com',

            'username' => 'johnDoe',

            'password' => 'password123',       
        ]);

        $tweet1 = Tweet::create([

            'body' => 'First tweet',

            'user_id' => $user->id,
        ]);

        $tweet2 = Tweet::create([

            'body' => 'Second tweet',

            'user_id' => $user->id,
        ]);

        $response = $this->get('/api/user/tweets/{user_id}');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Tweets displayed successfully',
        
    //         'Tweets' =>

    //         [
    //             'id' => $tweet1->id,

    //             'body' => 'First tweet',

    //             'user_id' => $user->id,

    //             'user' => [
                
    //                 'first_name' => 'John',
    
    //                 'last_name' => 'Doe',
        
    //                 'email' => 'john.doe@example.com',
        
    //                 'username' => 'johnDoe',
        
    //                 'password' => 'password123',     
    //             ],
    
    //         ],

    //         [
    //             'id' => $tweet2->id,

    //             'body' => 'Second tweet',

    //             'user_id' => $user->id,

    //             'user' => [
                
    //                 'first_name' => 'John',
    
    //                 'last_name' => 'Doe',
        
    //                 'email' => 'john.doe@example.com',
        
    //                 'username' => 'johnDoe',
        
    //                 'password' => 'password123',     
    //             ],
    
    //         ]

        ]);
    }
}
