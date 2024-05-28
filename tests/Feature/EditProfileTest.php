<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_update_profile(): void
    {
        $user = User::create([

            'first_name' => 'Kings',

            'last_name' => 'John',

            'email' => 'John@gmail.com',

            'username' => 'John',

            'password' => 'john1234',

        ]);

        $profile = Profile::create([

            'user_id' => 1,

            'name' => 'John',

            'bio' => 'consistency'
        ]);

        $request = [

            'user_id' => 1,

            'name' => 'Doe',

            'location' => 'New York',

            'bio' => 'new account'
        ];


        $response = $this->put('/api/update/1',$request);

        $response->assertStatus(200);

        $response->assertJson([

            'message' => 'Profile updated successfully'

        ]);

    }

}
