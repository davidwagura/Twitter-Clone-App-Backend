<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserFollowerUnfollowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_follower_unfollow(): void
    {
        $user = User::create([

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'Doe@gmail.com',

            'username' => 'Doe',

            'password' => 'John1234'

        ]);

        $follower = User::create([

            'first_name' => 'Peter',

            'last_name' => 'Smith',

            'email' => 'Peter@gmail.com',

            'username' => 'Smith',

            'password' => 'Peter1234'

        ]);

        $response = $this->post('/api/unfollow/1/1');

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Unfollow successful'

        ]);

        $response->assertJsonFragment([

            'id' => 1,

            'first_name' => 'John'
        ]);

    }

}
