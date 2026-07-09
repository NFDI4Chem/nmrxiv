<?php

namespace Tests\Feature\Project;

use Database\Seeders\RolesAndPermissionsSeeder;
use Tests\TestCase;

abstract class ProjectFeatureTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }
}
