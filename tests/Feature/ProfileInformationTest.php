<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->put('/user/profile-information', [
            'name' => 'Test Name',
            'first_name' => 'Test',
            'last_name' => 'Name',
            'username' => 'test',
            'email' => 'test@example.com',
            'orcid_id' => 'test',
            'affiliation' => 'test',
        ]);

        $this->assertEquals('Test', $user->fresh()->first_name);
        $this->assertEquals('Name', $user->fresh()->last_name);
        $this->assertEquals('test@example.com', $user->fresh()->email);
    }
}
