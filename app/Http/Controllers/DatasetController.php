<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetResource;
use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Sample;
use App\Models\User;
use App\Services\InteractionTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class DatasetController extends Controller
{
    //
    public function publicDatasetView(Request $request, $slug)
    {
        $dataset = Dataset::where('slug', $slug)->firstOrFail();

        if (! $dataset->is_public) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        app(InteractionTracker::class)->recordView($request, false, $dataset);

        return Inertia::render('Public/Dataset', [
            'dataset' => $dataset,
        ]);
    }

    public function fetchNMRium(Request $request, Dataset $dataset)
    {
        $dataset->loadMissing(['study.sample']);

        return $dataset->normalizedNmriumInfo();
    }

    public function nmriumInfo(Request $request, Dataset $dataset)
    {
        if ($dataset) {
            $user = Auth::user();
            $data = $request->get('data');
            $version = $request->get('version');
            $spectra = $request->get('spectra');
            $molecules = $request->get('molecules');

            $nmriumInfo = sanitizeUnicodeInArray($spectra);
            $molecularInfo = sanitizeUnicodeInArray($molecules);
            $molecularInfo = $this->normalizeMoleculeHeaders($molecularInfo);

            $nmrium = $dataset->nmrium;
            if ($nmrium) {
                $nmriumData = $nmrium->nmrium_info ?: [];
            } else {
                $nmriumData = [];
            }
            if ($nmriumInfo && ! empty($nmriumInfo)) {
                $nmriumData['spectra'] = $nmriumInfo;
            }
            if ($molecularInfo && ! empty($molecularInfo)) {
                $nmriumData['molecules'] = $molecularInfo;
            }

            if ($version && ! empty($version)) {
                $nmriumData['version'] = $version;
            }

            if (! empty($nmriumData)) {
                if ($nmrium) {
                    $nmrium->nmrium_info = $nmriumData;
                    $dataset->has_nmrium = true;
                    $nmrium->save();
                } else {
                    $nmrium = NMRium::create([
                        'nmrium_info' => $nmriumData,
                    ]);
                    $dataset->nmrium()->save($nmrium);
                    $dataset->has_nmrium = true;
                }

                foreach ($spectra as $spectrum) {
                    if (! is_array($spectrum)) {
                        continue;
                    }
                    $label = $this->spectrumTypeLabel($spectrum);
                    if ($label !== null) {
                        $dataset->type = $label;
                    }
                }

                $dataset->save();

                return $dataset->fresh();
            }
        }
    }

    public function nmriumVersions(Request $request, Dataset $dataset)
    {
        if ($dataset) {
            $nmrium = $dataset->nmrium;

            if ($nmrium) {
                return $nmrium->versions()->orderBy('created_at', 'DESC')->get()->map(function ($version) {
                    $user = User::find($version->user_id);

                    return [
                        'updated_at' => $version->updated_at,
                        'user' => [
                            'name' => $user->first_name.' '.$user->last_name,
                            'profile_photo_url' => $user->profile_photo_url,
                        ],
                    ];
                });
            }
        }
    }

    public function publicDatasetsView(Request $request)
    {
        // $datasets = Cache::rememberForever('datasets', function () {
        $datasets = DatasetResource::collection(Dataset::with('study')->where([['is_public', true], ['is_archived', false]])->filter($request->only('search', 'sort', 'mode'))->paginate(12)->withQueryString());
        // });

        return Inertia::render('Public/Datasets', [
            'filters' => $request->all('search', 'sort', 'mode'),
            'datasets' => $datasets,
        ]);
    }

    /**
     * Save the user-supplied spectrum assignment block for a dataset.
     *
     * Authorisation rides on the parent study's `updateStudy` gate so that
     * write-access to assignments mirrors the rest of the per-sample
     * upload work (avoids inventing a new dataset-level policy for a field
     * that conceptually belongs to the same edit session).
     *
     * Accepts:
     *   - `acs`         (string, optional) free-form ACS-style string
     *   - `atom_peaks`  (array,  optional) list of `{atom, peak, label?}`
     *   - `source`      (string, optional) `manual` | `nmrium` (defaults to `manual`)
     *
     * Pass an empty payload (no `acs`, no `atom_peaks`) to clear the field.
     */
    public function updateAssignments(Request $request, Dataset $dataset)
    {
        $study = $dataset->study;
        if (! $study) {
            throw ValidationException::withMessages([
                'dataset' => 'Dataset is not attached to a study.',
            ]);
        }
        Gate::forUser($request->user())->authorize('updateStudy', $study);

        $validated = $request->validate([
            'acs' => 'nullable|string|max:65535',
            'atom_peaks' => 'nullable|array',
            'atom_peaks.*.atom' => 'nullable|string|max:64',
            'atom_peaks.*.peak' => 'nullable',
            'atom_peaks.*.label' => 'nullable|string|max:255',
            'source' => 'nullable|string|in:manual,nmrium',
        ]);

        $acs = isset($validated['acs']) ? trim((string) $validated['acs']) : '';
        $atomPeaks = $validated['atom_peaks'] ?? [];

        if ($acs === '' && empty($atomPeaks)) {
            $dataset->assignments = null;
        } else {
            $dataset->assignments = [
                'acs' => $acs,
                'atom_peaks' => array_values($atomPeaks),
                'source' => $validated['source'] ?? 'manual',
                'updated_at' => now()->toIso8601String(),
            ];
        }

        $dataset->save();

        return response()->json([
            'id' => $dataset->id,
            'assignments' => $dataset->assignments,
            'has_assignments' => $dataset->hasAssignments(),
        ]);
    }

    public function snapshot(Request $request, Dataset $dataset)
    {
        $content = $request->get('img');
        $study = $dataset->study;
        if ($content) {
            if ($study->project) {
                $path = '/projects/'.$study->project->uuid.'/'.$study->uuid.'/'.$dataset->slug.'.svg';
                Storage::disk(config('filesystems.default_public'))->put($path, $content, 'public');
                $dataset->dataset_photo_path = $path;
                $dataset->save();
            } else {
                $path = '/samples/'.$study->uuid.'/'.$dataset->slug.'.svg';
                Storage::disk(config('filesystems.default_public'))->put($path, $content, 'public');
                $dataset->dataset_photo_path = $path;
                $dataset->save();
            }
        }
    }

    /**
     * Ensure each molecule entry keeps a valid 3-line MOL header (title,
     * generator, comment) before the V2000/V3000 counts line. NMRium and the
     * chemistry standardize endpoint occasionally emit molfiles without the
     * title line, which silently breaks parsers on subsequent reload. We
     * never strip or escape the line; we only prepend blank lines (or the
     * molecule's label) when the header has been collapsed below 3 lines.
     * See `Sample::ensureMolfileHeader` for details.
     *
     * @param  array<int, mixed>|null  $molecules
     * @return array<int, mixed>|null
     */
    protected function normalizeMoleculeHeaders($molecules)
    {
        if (! is_array($molecules)) {
            return $molecules;
        }

        foreach ($molecules as $idx => $entry) {
            if (! is_array($entry)) {
                continue;
            }
            $molfile = $entry['molfile'] ?? null;
            if (! is_string($molfile) || $molfile === '') {
                continue;
            }
            $label = isset($entry['label']) ? (string) $entry['label'] : '';
            $molecules[$idx]['molfile'] = Sample::ensureMolfileHeader($molfile, $label);
        }

        return $molecules;
    }

    /**
     * Build a human-readable spectrum type label such as `1H NMR - 1D` from
     * an NMRium spectrum payload, falling back to a path-based dimensionality
     * guess when the parser-derived `info` block is missing or has been
     * corrupted by a legacy save (older builds dropped `experiment`/`nucleus`).
     *
     * @param  array<string, mixed>  $spectrum
     */
    protected function spectrumTypeLabel(array $spectrum): ?string
    {
        $info = is_array($spectrum['info'] ?? null) ? $spectrum['info'] : [];

        $experiment = isset($info['experiment']) && is_string($info['experiment']) && $info['experiment'] !== ''
            ? $this->formatExperimentName($info['experiment'])
            : null;

        $nucleus = $info['nucleus'] ?? null;
        if (is_array($nucleus)) {
            $nucleus = implode('-', array_filter(array_map('strval', $nucleus), fn ($v) => $v !== ''));
        } elseif (! is_string($nucleus)) {
            $nucleus = null;
        }
        if ($nucleus === '') {
            $nucleus = null;
        }

        $dimension = null;
        if (isset($info['dimension']) && is_numeric($info['dimension'])) {
            $dimension = (int) $info['dimension'];
        } else {
            $dimension = $this->guessSpectrumDimension($spectrum);
        }

        if ($experiment === null && $dimension === null && $nucleus === null) {
            return null;
        }

        if ($experiment === null && $dimension !== null) {
            $experiment = $dimension.'D';
        }

        if ($nucleus !== null && $experiment !== null) {
            return $nucleus.' NMR - '.$experiment;
        }

        if ($nucleus !== null) {
            return $nucleus.' NMR';
        }

        return ($experiment ?? '').' NMR';
    }

    /**
     * Render NMRium's lowercase experiment tokens (`hsqc`, `cosy`, `1d`, …)
     * in their conventional uppercase form for display. Unknown values are
     * passed through unchanged.
     */
    protected function formatExperimentName(string $experiment): string
    {
        $trimmed = trim($experiment);
        if ($trimmed === '') {
            return $experiment;
        }

        $lower = strtolower($trimmed);

        if (preg_match('/^(\d+)d$/', $lower, $m)) {
            return $m[1].'D';
        }

        $known = [
            'cosy', 'noesy', 'roesy', 'tocsy', 'hsqc', 'hmbc', 'hmqc',
            'dept', 'dept45', 'dept90', 'dept135', 'jres', 'inadequate',
            'apt', 'edited-hsqc', 'hsqc-tocsy',
        ];
        if (in_array($lower, $known, true)) {
            return strtoupper($lower);
        }

        return $trimmed;
    }

    /**
     * Heuristically infer 1D vs 2D from an NMRium spectrum's source selector
     * file paths (Bruker conventions: `acqu2s` / `pdata/.../2[ri]+` => 2D,
     * `acqus` / `pdata/.../1[ri]` / `fid` => 1D).
     *
     * @param  array<string, mixed>  $spectrum
     */
    protected function guessSpectrumDimension(array $spectrum): ?int
    {
        $selector = $spectrum['sourceSelector'] ?? $spectrum['selector'] ?? [];
        $files = is_array($selector['files'] ?? null) ? $selector['files'] : [];
        if (empty($files)) {
            return null;
        }

        foreach ($files as $file) {
            if (! is_string($file)) {
                continue;
            }
            $base = strtolower(basename($file));
            if (in_array($base, ['acqu2s', 'acqu3s', '2rr', '2ri', '2ir', '2ii', '3rrr'], true)) {
                return $base === '3rrr' ? 3 : 2;
            }
            if (preg_match('#/pdata/\d+/2[ri]+$#i', $file)) {
                return 2;
            }
        }

        foreach ($files as $file) {
            if (! is_string($file)) {
                continue;
            }
            $base = strtolower(basename($file));
            if (in_array($base, ['acqus', '1r', '1i', 'fid'], true)) {
                return 1;
            }
        }

        return null;
    }
}
