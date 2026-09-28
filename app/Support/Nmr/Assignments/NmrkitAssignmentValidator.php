<?php

namespace App\Support\Nmr\Assignments;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Client for NMRKit's `POST /latest/validate/assignments`, which checks the
 * assignments against predicted shifts.
 */
class NmrkitAssignmentValidator
{
    public const UNAVAILABLE_MESSAGE = 'The shift prediction is currently unavailable. Please try again later.';

    /**
     * @return array<string, mixed>
     *
     * @throws InvalidAssignmentSetException
     * @throws AssignmentValidationUnavailableException
     */
    public function validate(AssignmentSet $set): array
    {
        try {
            $response = Http::acceptJson()
                ->timeout((int) config('nmrxiv.assignment_validation.timeout'))
                ->post((string) config('nmrxiv.assignment_validation.url'), $set->toPayload());
        } catch (ConnectionException $exception) {
            throw new AssignmentValidationUnavailableException('NMRKit could not be reached.', previous: $exception);
        }

        if ($response->status() === 422) {
            throw new InvalidAssignmentSetException($this->errorMessage($response, 'The structure or assignments were rejected.'));
        }

        if ($response->failed()) {
            throw new AssignmentValidationUnavailableException(self::UNAVAILABLE_MESSAGE);
        }

        $report = $response->json();
        if (! is_array($report) || ! isset($report['verdict'])) {
            throw new AssignmentValidationUnavailableException('NMRKit returned an unexpected response.');
        }

        return $report;
    }

    private function errorMessage(Response $response, string $fallback): string
    {
        $detail = $response->json('detail');

        if (is_array($detail) && is_string($detail['message'] ?? null)) {
            return $detail['message'];
        }
        if (is_string($detail) && $detail !== '') {
            return $detail;
        }
        if (is_array($detail) && is_array($detail[0] ?? null) && is_string($detail[0]['msg'] ?? null)) {
            return $detail[0]['msg'];
        }

        return $fallback;
    }
}
