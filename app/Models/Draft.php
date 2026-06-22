<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Tags\HasTags;

class Draft extends Model
{
    use HasFactory;
    use HasTags;

    public const DEPOSITION_COMMUNITY = 'community';

    public const LEGACY_COMMUNITY_NAME_PREFIX = 'Community Contribution (Draft:';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'relative_url',
        'path',
        'key',
        'is_deleted',
        'owner_id',
        'team_id',
        'settings',
        'info',
        'project_enabled',
        'current_step',
        'eln',
        'external_id',
        'callback_url',
        'zip_url',
        'release_date',
        'status',
        'processing_logs',
        'tracking_item_name',
    ];

    protected $casts = [
        'info' => 'array',
        'settings' => 'array',
        'processing_logs' => 'array',
        'release_date' => 'date',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(FileSystemObject::class);
    }

    /**
     * Get the owner of the draft.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the project associated with the draft.
     */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    /**
     * Get the team associated with the draft.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function isCommunityContribution(): bool
    {
        if (($this->settings['deposition_type'] ?? null) === self::DEPOSITION_COMMUNITY) {
            return true;
        }

        return str_starts_with($this->name ?? '', self::LEGACY_COMMUNITY_NAME_PREFIX);
    }

    /**
     * @param  Builder<Draft>  $query
     * @return Builder<Draft>
     */
    public function scopeCommunityContribution(Builder $query): Builder
    {
        return $query->where(function (Builder $community): void {
            $community->where('settings->deposition_type', self::DEPOSITION_COMMUNITY)
                ->orWhere('name', 'like', self::LEGACY_COMMUNITY_NAME_PREFIX.'%');
        });
    }
}
