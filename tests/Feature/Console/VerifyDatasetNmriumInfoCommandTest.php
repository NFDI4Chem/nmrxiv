<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyDatasetNmriumInfoCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_runs_with_no_datasets(): void
    {
        $this->artisan('nmrxiv:verify-dataset-nmrium-info')
            ->expectsOutputToContain('No datasets in the database.')
            ->assertSuccessful();
    }
}
