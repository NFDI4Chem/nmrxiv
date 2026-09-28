<?php

namespace App\Models;

use App\Enums\AssignmentSource;
use App\Enums\AssignmentValidationStatus;
use App\Support\Nmr\Assignments\AssignmentSet;
use Database\Factories\AssignmentValidationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One nmrshiftdb2 Quickcheck run of a sample's assignments. The author can
 * confirm a completed run; the prediction is a reference, so confirming an
 * inconsistent result is allowed but needs a note.
 */
class AssignmentValidation extends Model
{
    /** @use HasFactory<AssignmentValidationFactory> */
    use HasFactory;

    protected $fillable = [
        'study_id',
        'requested_by',
        'source',
        'input',
        'input_hash',
        'status',
        'report',
        'verdict',
        'assignment_result',
        'mark_13c',
        'mark_1h',
        'error',
        'completed_at',
        'confirmed_by',
        'confirmed_at',
        'confirmation_note',
    ];

    protected function casts(): array
    {
        return [
            'source' => AssignmentSource::class,
            'status' => AssignmentValidationStatus::class,
            'input' => 'array',
            'report' => 'array',
            'mark_13c' => 'integer',
            'mark_1h' => 'integer',
            'completed_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public static function queueFor(Study $study, AssignmentSet $set, ?User $requester): self
    {
        return self::create([
            'study_id' => $study->id,
            'requested_by' => $requester?->id,
            'source' => $set->source,
            'input' => $set->toPayload(),
            'input_hash' => $set->hash(),
            'status' => AssignmentValidationStatus::Queued,
        ]);
    }

    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function assignmentSet(): AssignmentSet
    {
        return AssignmentSet::fromPayload($this->input ?? [], $this->source);
    }

    public function markRunning(): void
    {
        $this->update(['status' => AssignmentValidationStatus::Running, 'error' => null]);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function markCompleted(array $report): void
    {
        $this->update([
            'status' => AssignmentValidationStatus::Completed,
            'report' => $report,
            'verdict' => $report['verdict'] ?? null,
            'assignment_result' => $report['assignment_check']['result'] ?? null,
            'mark_13c' => $report['reports']['13C']['mark'] ?? null,
            'mark_1h' => $report['reports']['1H']['mark'] ?? null,
            'error' => null,
            'completed_at' => now(),
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status' => AssignmentValidationStatus::Failed,
            'error' => $error,
            'completed_at' => now(),
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === AssignmentValidationStatus::Completed;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed_at !== null;
    }

    /**
     * Confirming against the prediction's verdict should be explained, e.g.
     * "new skeleton; C-4 supported by HMBC to H-2/H-6".
     */
    public function requiresConfirmationNote(): bool
    {
        return $this->assignment_result === 'inconsistent' || $this->verdict === 'reject';
    }

    /**
     * NMRium assignments can change after the check; a run is stale once
     * they no longer match what was checked.
     */
    public function isStaleAgainst(?AssignmentSet $current): bool
    {
        if ($this->source !== AssignmentSource::Nmrium) {
            return false;
        }

        return $current === null || $current->hash() !== $this->input_hash;
    }

    public function confirm(User $user, ?string $note): void
    {
        $this->update([
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
            'confirmation_note' => $note !== null && trim($note) !== '' ? trim($note) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(bool $withReport = true, bool $stale = false): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'source' => $this->source->value,
            'source_label' => $this->source->label(),
            'verdict' => $this->verdict,
            'assignment_result' => $this->assignment_result,
            'marks' => ['13C' => $this->mark_13c, '1H' => $this->mark_1h],
            'error' => $this->error,
            'stale' => $stale,
            'requires_confirmation_note' => $this->requiresConfirmationNote(),
            'confirmed' => $this->isConfirmed(),
            'confirmed_at' => $this->confirmed_at?->toIso8601String(),
            'confirmed_by' => $this->confirmer?->name,
            'confirmation_note' => $this->confirmation_note,
            'created_at' => $this->created_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'report' => $withReport ? $this->report : null,
            'molfile' => $withReport ? ($this->input['structure']['molfile'] ?? null) : null,
        ];
    }
}
