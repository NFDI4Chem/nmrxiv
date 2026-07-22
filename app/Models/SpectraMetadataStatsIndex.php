<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpectraMetadataStatsIndex extends Model
{
    public const SCOPE_PUBLIC_INDEXED = 'public_indexed';

    protected $table = 'spectra_metadata_stats_index';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'scope',
        'totals',
        'distributions',
        'missing',
        'computed_at',
    ];

    protected function casts(): array
    {
        return [
            'totals' => 'array',
            'distributions' => 'array',
            'missing' => 'array',
            'computed_at' => 'datetime',
        ];
    }

    /**
     * @return array{
     *     scope: string,
     *     totals: array<string, mixed>,
     *     distributions: array<string, list<array{value: string, count: int}>>,
     *     missing: array<string, int>
     * }
     */
    public function toStatisticsPayload(int $distributionLimit = 50): array
    {
        $distributionLimit = max(1, min(200, $distributionLimit));

        $distributions = collect($this->distributions ?? [])
            ->map(fn (array $rows): array => array_slice($rows, 0, $distributionLimit))
            ->all();

        return [
            'scope' => $this->scope,
            'totals' => $this->totals ?? [],
            'distributions' => $distributions,
            'missing' => $this->missing ?? [],
        ];
    }
}
