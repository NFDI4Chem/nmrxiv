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

    public function test_profile_information_can_be_updated_with_ror_id(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->put('/user/profile-information', [
            'name' => 'Test Name',
            'first_name' => 'Test',
            'last_name' => 'Name',
            'username' => 'testror',
            'email' => 'testror@example.com',
            'orcid_id' => 'test',
            'affiliation' => 'Friedrich Schiller University Jena (FSU, Friedrich-Schiller-Universität Jena) - Education · Jena, Germany',
            'ror_id' => 'https://ror.org/05qghxh33',
        ]);

        $this->assertEquals('Test', $user->fresh()->first_name);
        $this->assertEquals('Name', $user->fresh()->last_name);
        $this->assertEquals('testror@example.com', $user->fresh()->email);
        $this->assertEquals('Friedrich Schiller University Jena (FSU, Friedrich-Schiller-Universität Jena) - Education · Jena, Germany', $user->fresh()->affiliation);
        $this->assertEquals('https://ror.org/05qghxh33', $user->fresh()->ror_id);
    }

    public function test_profile_information_ror_id_can_be_removed(): void
    {
        $user = User::factory()->create([
            'affiliation' => 'Friedrich Schiller University Jena (FSU, Friedrich-Schiller-Universität Jena) - Education · Jena, Germany',
            'ror_id' => 'https://ror.org/05qghxh33',
        ]);

        $this->actingAs($user);

        $response = $this->put('/user/profile-information', [
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'orcid_id' => $user->orcid_id,
            'affiliation' => 'Independent Researcher',
            'ror_id' => null,
        ]);

        $this->assertEquals('Independent Researcher', $user->fresh()->affiliation);
        $this->assertNull($user->fresh()->ror_id);
    }

    public function test_profile_information_can_be_updated_without_ror_id(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->put('/user/profile-information', [
            'name' => 'Test Name',
            'first_name' => 'Test',
            'last_name' => 'Name',
            'username' => 'testnoror',
            'email' => 'testnoror@example.com',
            'orcid_id' => 'test',
            'affiliation' => 'Small Research Institute',
        ]);

        $this->assertEquals('Small Research Institute', $user->fresh()->affiliation);
        $this->assertNull($user->fresh()->ror_id);
    }
}
