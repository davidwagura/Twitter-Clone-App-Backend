<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserConversationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_user_conversation(): void
    {
        $sender = User::create([

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

        ]);

        $receiver = User::create([

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

        ]);

        $message = Message::create([

            'body' => 'Good morning',

            'sender_id' => $sender->id,

            'receivers_id' => $receiver->id

        ]);

        $response = $this->get('/api/conversations/1/2');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Conversation displayed successfully'

        ]);

    }

}
