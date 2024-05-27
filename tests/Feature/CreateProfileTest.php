<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Profile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_create_profile(): void
    {
        $profile = Profile::created([

            'id' => 1,

            'name' => 'John',

            'bio' => 'consistency'

        ]);
        
        $response = $this->post('/api/profile');

        $response->assertStatus(200);

        $response->assertJson([
           
            'message' => 'Profile created successfully'

        ]);

    }

}
