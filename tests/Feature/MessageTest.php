<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_new_message(): void
    {
        $sender = User::create([

            'id' => 1,

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',


        ]);

        $receiver = User::create([

            'id' => 1,

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',


        ]);

        $data = [

            'id' => 1,

            'body' => 'Good morning',

            'sender_id' => $sender->id,

            'receivers_id' => $receiver->id
        ];

        $response = $this->post('/api/messages/1/1',$data);

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Message sent successfully'

        ]);
    }

}
