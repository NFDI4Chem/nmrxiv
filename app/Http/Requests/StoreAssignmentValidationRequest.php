<?php

namespace App\Http\Requests;

use App\Models\Study;

/**
 * Queues a Quickcheck for a sample, from its NMRium assignments, an uploaded
 * SD file or manual rows.
 */
class StoreAssignmentValidationRequest extends QuickcheckAssignmentsRequest
{
    public function authorize(): bool
    {
        $study = $this->route('study');

        return $study instanceof Study
            && $this->user()?->can('updateStudy', $study) === true;
    }

    /**
     * @return list<string>
     */
    protected function allowedSources(): array
    {
        return ['nmrium', 'file', 'manual'];
    }
}
