<?php

namespace Tests\Feature\Console;

use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Study;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Mockery;
use Tests\TestCase;

class RepairMissingCompoundInfoCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Fake the nested Artisan::call made by the repair command while letting
     * the outer command (run via $this->artisan) execute for real. A proxy
     * partial mock wraps the live kernel, so any call that does not match the
     * expectation is forwarded to the actual implementation.
     *
     * @param  callable(string, array<string, mixed>): bool  $matcher
     */
    private function fakeNestedArtisanCall(callable $matcher): void
    {
        $kernel = Mockery::mock($this->app->make(Kernel::class))->makePartial();
        $kernel->shouldReceive('call')
            ->once()
            ->withArgs($matcher)
            ->andReturn(0);

        Artisan::swap($kernel);
    }

    public function test_exits_successfully_when_nothing_needs_repair(): void
    {
        Molecule::factory()->create([
            'standard_inchi' => 'InChI=1S/C6H6/c1-2-4-6-5-3-1/h1-6H',
            'iupac_name' => 'benzene',
            'molecular_formula' => 'C6H6',
            'molecular_weight' => 78.11,
            'canonical_smiles' => 'c1ccccc1',
            'cas' => '71-43-2',
        ]);

        $this->artisan('nmrxiv:repair-missing-compound-info')
            ->expectsOutputToContain('No molecules with missing compound metadata.')
            ->expectsOutputToContain('No studies with missing NMRium spectrum metadata.')
            ->assertSuccessful();
    }

    public function test_command_is_scheduled_every_five_minutes_on_one_server(): void
    {
        $event = collect(Schedule::events())->first(
            fn ($scheduled) => str_contains((string) $scheduled->command, 'nmrxiv:repair-missing-compound-info')
        );

        $this->assertNotNull($event);
        $this->assertSame('*/5 * * * *', $event->expression);
        $this->assertTrue($event->withoutOverlapping);
        $this->assertTrue($event->onOneServer);
    }

    public function test_dispatches_molecule_clean_when_metadata_is_missing(): void
    {
        Molecule::factory()->create([
            'standard_inchi' => 'InChI=1S/C6H6/c1-2-4-6-5-3-1/h1-6H',
            'iupac_name' => null,
            'molecular_formula' => '',
            'molecular_weight' => null,
        ]);

        $this->fakeNestedArtisanCall(fn (string $command, array $parameters = []): bool => $command === 'nmrxiv:molecules-clean'
            && ($parameters['--force'] ?? false) === true
            && ($parameters['--limit'] ?? 0) === 1);

        $this->artisan('nmrxiv:repair-missing-compound-info', ['--molecules' => true])
            ->expectsOutputToContain('Found 1 molecule(s) with missing compound metadata')
            ->assertSuccessful();
    }

    public function test_skips_nmrium_repair_when_nmrkit_url_is_not_configured(): void
    {
        config(['external-links.nmrkit_url' => '']);

        $this->artisan('nmrxiv:repair-missing-compound-info', ['--nmrium' => true])
            ->expectsOutputToContain('NMRKIT_URL is not configured')
            ->assertSuccessful();
    }

    public function test_dispatches_nmrium_refresh_when_spectrum_metadata_is_missing(): void
    {
        config(['external-links.nmrkit_url' => 'https://nmrkit.example']);

        $study = Study::factory()->create();

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => []],
                    ],
                ],
            ],
        ]);

        $this->fakeNestedArtisanCall(fn (string $command, array $parameters = []): bool => $command === 'nmrxiv:refresh-nmrium-info'
            && (int) ($parameters['--study'] ?? 0) === $study->id);

        $this->artisan('nmrxiv:repair-missing-compound-info', ['--nmrium' => true])
            ->expectsOutputToContain('Found 1 study/studies with missing NMRium spectrum metadata')
            ->assertSuccessful();
    }
}
