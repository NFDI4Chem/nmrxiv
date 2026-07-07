<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishReleasedProjectsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_embargo_projects_command_exits_successfully(): void
    {
        $this->artisan('nmrxiv:publish-embargo-projects')
            ->assertExitCode(0);
    }
}
