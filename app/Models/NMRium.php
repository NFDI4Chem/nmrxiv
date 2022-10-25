<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function dataset()
    {
        return $this->belongsTo(Dataset::class, 'dataset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
