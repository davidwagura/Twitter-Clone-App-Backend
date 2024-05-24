<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function reset_password(): void
    {
        // $user = User::create([

        //     'id' => 1,

        //     'first_name' => 'John',

        //     'last_name' => 'White',

        //     'email' => 'John@gmail.com',

        //     'username' => 'White',

        //     'password' => 'john1234'
        // ]);
        
        $response = $this->post('/api/resetPassword/{user_id}');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Password reset successful'

        ]);
    }
}
