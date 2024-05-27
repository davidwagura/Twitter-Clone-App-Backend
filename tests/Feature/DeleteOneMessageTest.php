<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteOneMessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_delete_one_message(): void
    {
        $sender = User::create([

            'id' => 2,

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',


        ]);

        $receiver = User::create([

            'id' => 2,

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

        ]);

        $message = Message::create([

            'id' => 1,

            'body' => 'Good morning',

            'sender_id' => $sender->id,

            'receivers_id' => $receiver->id
        ]);

        $response = $this->delete('/api/deleteOneMessage/1');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Message deleted successfully'

        ]);

    }
}
