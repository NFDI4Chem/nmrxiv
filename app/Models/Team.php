<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'compound_library_code',
    ];

    protected static function booted(): void
    {
        static::creating(function (Team $team): void {
            $team->compound_library_code ??= static::generateCompoundLibraryCode();
        });
    }

    /**
     * Unguessable identifier used in the shareable compound library URL.
     */
    public static function generateCompoundLibraryCode(): string
    {
        do {
            $code = Str::random(16);
        } while (static::query()->where('compound_library_code', $code)->exists());

        return $code;
    }

    public function compoundLibraryUrl(): string
    {
        return route('public.compound-library', ['team' => $this->compound_library_code]);
    }

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
        ];
    }

    /**
     * Get the default team profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function getProfilePhotoUrlAttribute()
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Team model and Project relationship - one to many
     *
     * @var array
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function activeProjects(): HasMany
    {
        return $this->hasMany(Project::class)->where([['is_deleted', false], ['is_archived', false]]);
    }
}
