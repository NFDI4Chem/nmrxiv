<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileSystemObject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uuid',
        'slug',
        'description',
        'relative_url',
        'path',
        'type',
        'key',
        'compressionInfo',
        'kernelSessionInfo',
        'color',
        'starred',
        'is_public',
        'is_deleted',
        'is_archived',
        'is_original',
        'is_verified',
        'is_processed',
        'is_root',
        'sort_order',
        'owner_id',
        'project_id',
        'study_id',
        'dataset_id',
        'draft_id',
        'version_id',
        'version',
        'parent_id',
        'settings',
        'info',
        'level',
        'has_children',
        // File integrity fields
        'checksum_md5',
        'checksum_sha256',
        'checksum_algorithm',
        'file_size',
        'integrity_status',
        'integrity_verified_at',
        'integrity_error',
        'verification_attempts',
        'last_verification_attempt',
        'external_url',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['download_url'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'integrity_verified_at' => 'datetime',
        'last_verification_attempt' => 'datetime',
        'verification_attempts' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Get the download URL to the file system object.
     *
     * @return string
     */
    public function getDownloadUrlAttribute()
    {
        if ($this->model_type == 'study') {
            return $this->study ? $this->study->download_url : null;
        }
    }

    /**
     * Check if the file has integrity verification pending.
     */
    public function hasIntegrityPending(): bool
    {
        return $this->type === 'file' && $this->integrity_status === 'pending';
    }

    /**
     * Check if the file integrity is verified.
     */
    public function isIntegrityVerified(): bool
    {
        return $this->type === 'file' && $this->integrity_status === 'verified';
    }

    /**
     * Check if the file integrity verification failed.
     */
    public function hasIntegrityFailed(): bool
    {
        return $this->type === 'file' && $this->integrity_status === 'failed';
    }

    /**
     * Get the primary checksum based on the algorithm.
     */
    public function getPrimaryChecksum(): ?string
    {
        return match ($this->checksum_algorithm) {
            'md5' => $this->checksum_md5,
            'sha256' => $this->checksum_sha256,
            default => $this->checksum_sha256,
        };
    }

    /**
     * Set integrity verification as failed with error message.
     */
    public function markIntegrityFailed(string $error): void
    {
        $this->update([
            'integrity_status' => 'failed',
            'integrity_error' => $error,
            'last_verification_attempt' => now(),
            'verification_attempts' => $this->verification_attempts + 1,
        ]);
    }

    /**
     * Set integrity verification as successful.
     */
    public function markIntegrityVerified(): void
    {
        $this->update([
            'integrity_status' => 'verified',
            'integrity_verified_at' => now(),
            'integrity_error' => null,
            'last_verification_attempt' => now(),
            'verification_attempts' => $this->verification_attempts + 1,
        ]);
    }

    public function children(): HasMany
    {
        return $this->hasMany(FileSystemObject::class, 'parent_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FileSystemObject::class, 'parent_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function draft(): BelongsTo
    {
        return $this->belongsTo(Draft::class, 'draft_id');
    }

    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class, 'study_id');
    }
}
