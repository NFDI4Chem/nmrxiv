<?php

namespace App\Models;

use Database\Factories\TeamMoleculeQualityScoreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMoleculeQualityScore extends Model
{
    /** @use HasFactory<TeamMoleculeQualityScoreFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'molecule_id',
        'tier',
        'breakdown',
        'rubric_version',
        'scored_at',
    ];

    protected function casts(): array
    {
        return [
            'tier' => 'integer',
            'breakdown' => 'array',
            'rubric_version' => 'integer',
            'scored_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function molecule(): BelongsTo
    {
        return $this->belongsTo(Molecule::class);
    }
}
