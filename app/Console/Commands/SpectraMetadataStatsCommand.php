<?php

namespace App\Console\Commands;

use App\Services\PublicMetadataSearchService;
use Illuminate\Console\Command;

class SpectraMetadataStatsCommand extends Command
{
    protected $signature = 'nmrxiv:spectra-metadata-stats
                            {--limit=50 : Maximum buckets per distribution when reading the index}
                            {--rebuild : Recompute and persist the statistics index before displaying}
                            {--json : Output raw JSON instead of tables}';

    protected $description = 'Show aggregate statistics for indexed public spectra metadata';

    public function __construct(private PublicMetadataSearchService $metadataSearch)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = max(1, min(200, (int) $this->option('limit')));

        if ($this->option('rebuild')) {
            $this->metadataSearch->refreshStatisticsIndex();
        }

        $stats = $this->metadataSearch->indexedCatalogStatistics($limit);

        if ($stats === null) {
            $this->metadataSearch->refreshStatisticsIndex();
            $stats = $this->metadataSearch->indexedCatalogStatistics($limit);
        }

        if ($stats === null) {
            $this->error('Unable to load spectra metadata statistics index.');

            return self::FAILURE;
        }

        if ($this->option('json')) {
            $this->line(json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $totals = $stats['totals'];

        $this->info('Public indexed spectra metadata');
        if (isset($stats['computed_at'])) {
            $this->line('Indexed at: '.$stats['computed_at']);
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Indexed spectra', number_format($totals['spectra_indexed'])],
                ['Samples with indexed spectra', number_format($totals['samples_with_indexed_spectra'])],
                ['All public spectra', number_format($totals['public_spectra'])],
                [
                    'Indexed coverage',
                    $totals['indexed_coverage_percent'] !== null
                        ? $totals['indexed_coverage_percent'].'%'
                        : 'n/a',
                ],
            ],
        );

        foreach ($stats['distributions'] as $field => $rows) {
            if ($rows === []) {
                continue;
            }

            $this->newLine();
            $this->info($this->distributionLabel($field).' ('.number_format($stats['missing'][$field] ?? 0).' missing)');

            $this->table(
                $this->distributionTableHeaders($field),
                $this->distributionTableRows($field, $rows),
            );
        }

        return self::SUCCESS;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<int, string>>
     */
    private function distributionTableRows(string $field, array $rows): array
    {
        return match ($field) {
            'nucleus_measuring_frequency_mhz' => collect($rows)
                ->flatMap(function (array $row): array {
                    $nucleus = (string) ($row['nucleus'] ?? '');
                    $frequencies = $row['frequencies'] ?? [];

                    if ($frequencies === []) {
                        return [[$nucleus, number_format((int) ($row['count'] ?? 0))]];
                    }

                    return collect($frequencies)
                        ->map(fn (array $frequency): array => [
                            $nucleus.' @ '.$frequency['value'].' MHz',
                            number_format((int) $frequency['count']),
                        ])
                        ->all();
                })
                ->all(),
            'dimension_experiment_breakdown' => collect($rows)
                ->flatMap(function (array $row): array {
                    $dimension = (string) ($row['dimension'] ?? '');
                    $breakdown = (string) ($row['breakdown'] ?? 'segment');
                    $segments = $row['segments'] ?? [];

                    if ($segments === []) {
                        return [[$dimension, number_format((int) ($row['count'] ?? 0))]];
                    }

                    return collect($segments)
                        ->map(fn (array $segment): array => [
                            $dimension.' / '.$segment['value'].' ('.$breakdown.')',
                            number_format((int) $segment['count']),
                        ])
                        ->all();
                })
                ->all(),
            default => collect($rows)
                ->map(fn (array $row): array => [
                    (string) ($row['value'] ?? ''),
                    number_format((int) ($row['count'] ?? 0)),
                ])
                ->all(),
        };
    }

    /**
     * @return list<string>
     */
    private function distributionTableHeaders(string $field): array
    {
        return match ($field) {
            'nucleus_measuring_frequency_mhz' => ['Nucleus @ MHz', 'Spectra'],
            'dimension_experiment_breakdown' => ['Dimension / Segment', 'Spectra'],
            default => ['Value', 'Spectra'],
        };
    }

    private function distributionLabel(string $field): string
    {
        return match ($field) {
            'dimension' => 'Dimension (1D / 2D)',
            'nucleus' => 'Nucleus',
            'solvent' => 'Solvent',
            'experiment' => 'Experiment type',
            'measuring_frequency_mhz' => 'Measuring frequency (MHz)',
            'nucleus_measuring_frequency_mhz' => 'Nucleus by measuring frequency (MHz)',
            'dimension_experiment_breakdown' => 'Dimension by nucleus / experiment',
            'manufacturer' => 'Manufacturer',
            'temperature_k' => 'Temperature (K)',
            'pulse_sequence' => 'Pulse sequence',
            'tube_diameter_mm' => 'Tube diameter (mm)',
            'number_of_scans' => 'Number of scans',
            'instrument_model' => 'Probe / instrument model',
            default => ucfirst(str_replace('_', ' ', $field)),
        };
    }
}
