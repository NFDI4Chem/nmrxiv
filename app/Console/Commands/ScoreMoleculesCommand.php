<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Support\Public\MoleculeQualityScorer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('nmrxiv:score-molecules
    {--molecule=* : Restrict to molecule id(s)}
    {--team=* : Restrict to team id(s) for team-scoped scores}
    {--skip-teams : Skip team-scoped contributor scores}
    {--stale : Only rescore rows with an older or missing rubric version}
    {--chunk=100 : Molecules per evidence-gathering chunk}
    {--dry : Compute without writing}')]
#[Description('Score public molecules for NMR data completeness and update contributor library stars')]
class ScoreMoleculesCommand extends Command
{
    public function handle(MoleculeQualityScorer $scorer): int
    {
        $chunk = max(1, (int) $this->option('chunk'));
        $dry = (bool) $this->option('dry');
        $staleOnly = (bool) $this->option('stale');
        $moleculeOptions = $this->option('molecule');
        $moleculeIds = filled($moleculeOptions)
            ? array_values(array_unique(array_map('intval', (array) $moleculeOptions)))
            : null;

        $global = $scorer->scoreGlobal($moleculeIds, $chunk, $staleOnly, $dry);
        $this->info(sprintf(
            '%s global molecule scores (%s scored, %s cleared).',
            $dry ? 'Would update' : 'Updated',
            number_format($global['scored']),
            number_format($global['cleared']),
        ));

        if ($this->option('skip-teams')) {
            return self::SUCCESS;
        }

        $teamOptions = $this->option('team');
        $teams = Team::query()
            ->when(filled($teamOptions), fn ($q) => $q->whereIn('id', array_map('intval', (array) $teamOptions)))
            ->whereNotNull('compound_library_code')
            ->orderBy('id')
            ->get();

        $teamScored = 0;
        $teamPruned = 0;
        foreach ($teams as $team) {
            $result = $scorer->scoreTeam($team, $moleculeIds, $chunk, $staleOnly, $dry);
            $teamScored += $result['scored'];
            $teamPruned += $result['pruned'];
        }

        $this->info(sprintf(
            '%s team-scoped scores across %s workspaces (%s scored, %s pruned).',
            $dry ? 'Would update' : 'Updated',
            number_format($teams->count()),
            number_format($teamScored),
            number_format($teamPruned),
        ));

        return self::SUCCESS;
    }
}
