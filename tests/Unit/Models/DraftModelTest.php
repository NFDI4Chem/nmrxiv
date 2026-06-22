<?php

namespace Tests\Unit\Models;

use App\Models\Draft;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_community_contribution_from_settings(): void
    {
        $draft = Draft::factory()->make([
            'settings' => ['deposition_type' => Draft::DEPOSITION_COMMUNITY],
            'name' => 'Untitled Project (Draft: abc123)',
        ]);

        $this->assertTrue($draft->isCommunityContribution());
    }

    public function test_is_community_contribution_from_legacy_name(): void
    {
        $draft = Draft::factory()->make([
            'settings' => [],
            'name' => Draft::LEGACY_COMMUNITY_NAME_PREFIX.' abc123)',
        ]);

        $this->assertTrue($draft->isCommunityContribution());
    }

    public function test_publication_draft_is_not_community_contribution(): void
    {
        $draft = Draft::factory()->make([
            'settings' => ['deposition_type' => 'publication'],
            'name' => 'Untitled Project (Draft: abc123)',
        ]);

        $this->assertFalse($draft->isCommunityContribution());
    }
}
