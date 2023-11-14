<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Mpociot\Versionable\VersionableTrait;

class NMRium extends Model
{
    use HasFactory;
    use VersionableTrait;

    protected $table = 'nmrium';

    protected $keepOldVersions = 10;

    protected $fillable = [
        'nmrium_info',
        'dataset_id',
    ];

    /**
     * Get the parent nmriumable model (Dataset or Study).
     */
    public function nmriumable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
