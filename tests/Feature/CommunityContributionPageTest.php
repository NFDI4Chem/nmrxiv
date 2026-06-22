<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityContributionPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_community_contribution_page_requires_authentication(): void
    {
        $response = $this->get(route('community-contribution'));

        $response->assertRedirect('/login');
    }

    public function test_community_contribution_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $page = $this->assertInertiaPageComponent(
            $this->actingAs($user)->get(route('community-contribution')),
            'CommunityContribution'
        );

        $this->assertArrayHasKey('draft', $page['props']);
        $this->assertSame('community', $page['props']['draft']['settings']['deposition_type'] ?? null);
    }

    public function test_two_users_receive_separate_community_drafts(): void
    {
        $firstUser = User::factory()->withPersonalTeam()->create();
        $secondUser = User::factory()->withPersonalTeam()->create();

        $firstPage = $this->assertInertiaPageComponent(
            $this->actingAs($firstUser)->get(route('community-contribution')),
            'CommunityContribution'
        );

        $secondPage = $this->assertInertiaPageComponent(
            $this->actingAs($secondUser)->get(route('community-contribution')),
            'CommunityContribution'
        );

        $this->assertNotSame(
            $firstPage['props']['draft']['id'],
            $secondPage['props']['draft']['id']
        );
    }

    public function test_community_contribution_reuses_existing_community_draft(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $firstResponse = $this->actingAs($user)->get(route('community-contribution'));
        $firstPage = $this->assertInertiaPageComponent($firstResponse, 'CommunityContribution');
        $draftId = $firstPage['props']['draft']['id'];

        $secondPage = $this->assertInertiaPageComponent(
            $this->actingAs($user)->get(route('community-contribution')),
            'CommunityContribution'
        );

        $this->assertSame($draftId, $secondPage['props']['draft']['id']);
    }
}
