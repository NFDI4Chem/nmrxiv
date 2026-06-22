<?php

namespace Tests\Unit\Policies;

use App\Models\Draft;
use App\Models\User;
use App\Policies\DraftPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_draft(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        [$userId] = $user->getUserTeamData();

        $draft = Draft::factory()->create(['owner_id' => $userId]);

        $policy = new DraftPolicy;

        $this->assertTrue($policy->updateDraft($user, $draft));
    }

    public function test_non_owner_cannot_update_draft(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        [$ownerId] = $owner->getUserTeamData();

        $otherUser = User::factory()->withPersonalTeam()->create();

        $draft = Draft::factory()->create(['owner_id' => $ownerId]);

        $policy = new DraftPolicy;

        $this->assertFalse($policy->updateDraft($otherUser, $draft));
    }
}
