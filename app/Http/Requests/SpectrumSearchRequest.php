<?php

namespace App\Http\Requests;

use App\Enums\PeakRule;
use App\Enums\SpectrumSearchMode;
use App\Support\Search\QueryPeak;
use App\Support\Search\SpectrumQueryParser;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SpectrumSearchRequest extends FormRequest
{
    /**
     * @var array<string, list<QueryPeak>>|null
     */
    private ?array $parsedPeaks = null;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $nuclei = $this->nuclei();
        $rules = [
            'peaks' => ['required', 'array:'.implode(',', $nuclei)],
            'mode' => ['nullable', Rule::enum(SpectrumSearchMode::class)],
            'closeness' => ['nullable', Rule::in(['strict', 'normal', 'relaxed'])],
            'tolerance' => ['nullable', 'array:'.implode(',', $nuclei)],
            'solvent' => ['nullable', 'string', 'max:255'],
            'same_solvent' => ['nullable', 'boolean'],
            'ignore_solvent_peaks' => ['nullable', 'boolean'],
            'allow_offset' => ['nullable', 'boolean'],
            'group' => ['nullable', Rule::in(['compound', 'dataset'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:24'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];

        foreach ($nuclei as $nucleus) {
            $rules["peaks.{$nucleus}"] = ['nullable', 'string', 'max:5000'];
            $rules["tolerance.{$nucleus}"] = ['nullable', 'numeric', 'gt:0', 'max:'.config("nmrxiv.spectra_search.max_tolerance.{$nucleus}")];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'peaks.required' => 'Add at least one peak to search for.',
            'peaks.array' => 'Peaks can be given for :values only.',
            'tolerance.*.max' => 'The closeness for :attribute can be at most :max ppm.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $parser = new SpectrumQueryParser;
            $this->parsedPeaks = [];
            foreach ($this->nuclei() as $nucleus) {
                $text = (string) $this->input("peaks.{$nucleus}", '');
                if (trim($text) === '') {
                    continue;
                }

                $parsed = $parser->parseText($text, $nucleus);
                foreach ($parsed['errors'] as $error) {
                    $validator->errors()->add("peaks.{$nucleus}", $error['message']);
                }
                if ($parsed['peaks'] !== []) {
                    $this->parsedPeaks[$nucleus] = $parsed['peaks'];
                }
            }

            $hasPeakToFind = collect($this->parsedPeaks)->flatten()
                ->contains(fn (QueryPeak $peak): bool => $peak->rule !== PeakRule::MustNot);
            if (! $hasPeakToFind) {
                $validator->errors()->add('peaks', 'Add at least one peak to search for.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => max(1, min(24, (int) $this->query('per_page', 12))),
            'page' => max(1, (int) $this->query('page', 1)),
        ]);
    }

    /**
     * Parsed query peaks per nucleus (only nuclei with peaks).
     *
     * @return array<string, list<QueryPeak>>
     */
    public function peaks(): array
    {
        return $this->parsedPeaks ?? [];
    }

    public function mode(): SpectrumSearchMode
    {
        return SpectrumSearchMode::tryFrom((string) $this->input('mode')) ?? SpectrumSearchMode::Contains;
    }

    /**
     * Tolerance in ppm per nucleus: an explicit override or the closeness preset.
     *
     * @return array<string, float>
     */
    public function tolerances(): array
    {
        $closeness = $this->input('closeness') ?: 'normal';

        return collect($this->nuclei())->mapWithKeys(fn (string $nucleus): array => [
            $nucleus => (float) ($this->input("tolerance.{$nucleus}") ?: config("nmrxiv.spectra_search.closeness.{$nucleus}.{$closeness}")),
        ])->all();
    }

    /**
     * @return array{solvent: ?string, same_solvent: bool, ignore_solvent_peaks: bool, allow_offset: bool, group: string, closeness: string}
     */
    public function options(): array
    {
        return [
            'solvent' => $this->filled('solvent') ? (string) $this->input('solvent') : null,
            'same_solvent' => $this->boolean('same_solvent'),
            'ignore_solvent_peaks' => $this->boolean('ignore_solvent_peaks', true),
            'allow_offset' => $this->boolean('allow_offset', true),
            'group' => $this->input('group') ?: 'compound',
            'closeness' => $this->input('closeness') ?: 'normal',
        ];
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 12);
    }

    public function page(): int
    {
        return (int) $this->input('page', 1);
    }

    /**
     * @return list<string>
     */
    private function nuclei(): array
    {
        return config('nmrxiv.spectra_search.nuclei', ['1H', '13C']);
    }
}
