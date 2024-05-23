<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUnretweetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_unretweet(): void
    {
        $user = User::create([
            
            'id' => 1,

            'first_name' => 'Alex',

            'last_name' => 'Williams',

            'email' => 'Alex@gmail.com',

            'username' => 'Alex',

            'password' => 'Alex1234'
        ]);

        $tweet = Tweet::create([

            'id' => 1,

            'body' => 'My new tweet',

            'user_id' => $user->id

        ]);

        $response = $this->post('/api/unretweet/1/1');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Unretweet successful'

        ]);
    }
}
