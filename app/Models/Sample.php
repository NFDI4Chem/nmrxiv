<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sample extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'sample_type',
        'source',
        'isa',
        'study_id',
        'project_id',
        'submitted_through',
    ];

    public function molecules(): BelongsToMany
    {
        return $this->belongsToMany(Molecule::class)
            ->withPivot('percentage_composition')
            ->withTimestamps();
    }

    /**
     * Get the study that owns the sample.
     */
    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class, 'study_id');
    }

    /**
     * Build NMRium-shaped molecule entries (`{id,label,molfile}`) from the
     * molecules associated with this sample. Used to hydrate NMRium when the
     * saved `data.molecules` array is empty but the sample already has
     * compounds attached (e.g. added via the sidebar before NMRium first
     * loaded the spectra). Returns an empty array if nothing can be hydrated.
     *
     * @return array<int, array{id:string,label:string,molfile:string}>
     */
    public function toNmriumMolecules(): array
    {
        $hydrated = [];

        foreach ($this->molecules()->get() as $molecule) {
            $molfile = $molecule->sdf;
            if (! is_string($molfile) || trim($molfile) === '') {
                continue;
            }

            $label = $molecule->iupac_name
                ?? $molecule->name
                ?? $molecule->cas
                ?? $molecule->canonical_smiles
                ?? $molecule->smiles
                ?? ('Molecule '.$molecule->id);

            $hydrated[] = [
                'id' => (string) ($molecule->inchi_key ?? $molecule->standard_inchi_key ?? $molecule->id),
                'label' => (string) $label,
                'molfile' => self::ensureMolfileHeader($molfile, (string) $label),
            ];
        }

        return $hydrated;
    }

    /**
     * MOL/SDF files require exactly 3 header lines before the counts line:
     *   line 1: title (compound name; may be empty but the line must exist)
     *   line 2: generator (software/timestamp/dimensions, e.g. "RDKit  2D")
     *   line 3: comment   (may be empty)
     *   line 4: counts    ("nat nbond ... V2000" or "  0  0 ... V3000")
     *
     * Both NMRium's drawing tool and the chemistry standardize endpoint can
     * emit molfiles where the title line is missing entirely, leaving only
     * 2 header lines. Most parsers (and NMRium itself on reload) silently
     * fail on those because they read the generator line as the title and
     * then misalign every subsequent line.
     *
     * This method detects the V2000/V3000 counts line and, if it sits at an
     * index < 3, prepends blank lines (or the supplied `$titleHint`) so the
     * counts line ends up at index 3. Already-well-formed molfiles are
     * returned unchanged. Non-molfile input is returned unchanged so this is
     * safe to call defensively.
     */
    public static function ensureMolfileHeader(string $molfile, string $titleHint = ''): string
    {
        if ($molfile === '') {
            return $molfile;
        }

        $normalized = str_replace(["\r\n", "\r"], "\n", $molfile);
        $lines = explode("\n", $normalized);

        $countsLineIdx = null;
        foreach ($lines as $idx => $line) {
            if (preg_match('/V2000\s*$/', $line) || preg_match('/V3000\s*$/', $line)) {
                $countsLineIdx = $idx;
                break;
            }
        }

        if ($countsLineIdx === null || $countsLineIdx >= 3) {
            return $molfile;
        }

        $missing = 3 - $countsLineIdx;
        $prepend = [];
        if ($missing > 0) {
            $prepend[] = $titleHint;
        }
        for ($i = 1; $i < $missing; $i++) {
            $prepend[] = '';
        }

        return implode("\n", array_merge($prepend, $lines));
    }

    /**
     * Merge sample-side molecules into a saved NMRium `data.molecules` array.
     * Strategy:
     *   1. Saved entries are kept in order (NMRium-drawn coordinates / labels
     *      win because NMRium uses `id` as its internal handle for assignments
     *      and spectra annotations).
     *   2. When a saved entry fingerprints to the same compound as a sample
     *      molecule but is missing `id` or `label`, those fields are filled
     *      in from the sample side. NMRium silently rejects molecules with an
     *      empty `id`, so this enrichment is what makes user-visible structure
     *      rendering work for samples uploaded via the sidebar/composition UI.
     *   3. Sample molecules whose fingerprint isn't represented in the saved
     *      list are appended at the end.
     *
     * Fingerprinting is structure-aware (atoms + bonds + element histogram) so
     * V2000 (RDKit-standardized) and V3000 (NMRium-drawn) variants of the same
     * compound collapse to one entry.
     *
     * @param  array<int, array<string, mixed>>  $savedMolecules
     * @return array<int, array<string, mixed>>
     */
    public function mergeNmriumMolecules(array $savedMolecules): array
    {
        $hydratedByFingerprint = [];
        foreach ($this->toNmriumMolecules() as $entry) {
            $hydratedByFingerprint[self::molfileFingerprint($entry['molfile'])] = $entry;
        }

        $merged = [];
        $seen = [];

        foreach ($savedMolecules as $entry) {
            if (! is_array($entry)) {
                $merged[] = $entry;

                continue;
            }

            $molfile = $entry['molfile'] ?? null;
            if (! is_string($molfile) || $molfile === '') {
                $merged[] = $entry;

                continue;
            }

            $fingerprint = self::molfileFingerprint($molfile);
            $matchingHydrated = $hydratedByFingerprint[$fingerprint] ?? null;

            $savedId = isset($entry['id']) ? trim((string) $entry['id']) : '';
            $savedLabel = isset($entry['label']) ? trim((string) $entry['label']) : '';

            if ($matchingHydrated !== null && ($savedId === '' || $savedLabel === '')) {
                $entry['id'] = $savedId !== '' ? $entry['id'] : $matchingHydrated['id'];
                $entry['label'] = $savedLabel !== '' ? $entry['label'] : $matchingHydrated['label'];
            }

            $entry['molfile'] = self::ensureMolfileHeader(
                $molfile,
                isset($entry['label']) ? (string) $entry['label'] : ''
            );

            $merged[] = $entry;
            $seen[$fingerprint] = true;
        }

        foreach ($hydratedByFingerprint as $fingerprint => $entry) {
            if (isset($seen[$fingerprint])) {
                continue;
            }
            $merged[] = $entry;
            $seen[$fingerprint] = true;
        }

        return $merged;
    }

    /**
     * Build a structure-aware fingerprint of a MOL/SDF block for dedup so that
     * the same compound serialized in different formats (V2000 vs V3000),
     * different generators (Actelion vs RDKit), or with different headers
     * still maps to the same key.
     *
     * The fingerprint is `"<atoms>:<bonds>:<sorted-element-histogram>"`.
     * Examples:
     *   benzene  -> "6:6:C6"
     *   ethanol  -> "3:2:C2O1"
     *   methane  -> "1:0:C1"
     *
     * This intentionally ignores stereochemistry, coordinates and connectivity
     * order so it is robust to RDKit canonicalization. It can collide for
     * positional isomers with the same molecular formula and ring count, which
     * is acceptable for the "is this molecule already shown?" question NMRium
     * needs answered. If a more rigorous comparison is ever required, prefer
     * an InChIKey-based dedup populated from the chem-standardize endpoint at
     * write time rather than re-parsing molfiles on every read.
     */
    private static function molfileFingerprint(string $molfile): string
    {
        $atoms = 0;
        $bonds = 0;
        $elements = [];

        $lines = preg_split("/\r\n|\r|\n/", $molfile) ?: [];
        $isV3000 = false;

        foreach ($lines as $line) {
            if (stripos($line, 'V3000') !== false) {
                $isV3000 = true;
                break;
            }
            if (stripos($line, 'V2000') !== false) {
                break;
            }
        }

        if ($isV3000) {
            foreach ($lines as $line) {
                if (preg_match('/^\s*M\s+V30\s+COUNTS\s+(\d+)\s+(\d+)/i', $line, $m)) {
                    $atoms = (int) $m[1];
                    $bonds = (int) $m[2];
                }
                if (preg_match('/^\s*M\s+V30\s+\d+\s+([A-Z][a-z]?)\s+-?\d/', $line, $m)) {
                    $sym = $m[1];
                    $elements[$sym] = ($elements[$sym] ?? 0) + 1;
                }
            }
        } else {
            $countsLineIdx = null;
            foreach ($lines as $idx => $line) {
                if (preg_match('/^\s*(\d+)\s+(\d+).*V2000/', $line, $m)) {
                    $atoms = (int) $m[1];
                    $bonds = (int) $m[2];
                    $countsLineIdx = $idx;
                    break;
                }
            }
            if ($countsLineIdx !== null) {
                for ($i = $countsLineIdx + 1; $i <= $countsLineIdx + $atoms && $i < count($lines); $i++) {
                    if (preg_match('/^\s*-?\d+\.\d+\s+-?\d+\.\d+\s+-?\d+\.\d+\s+([A-Z][a-z]?)/', $lines[$i], $m)) {
                        $sym = $m[1];
                        $elements[$sym] = ($elements[$sym] ?? 0) + 1;
                    }
                }
            }
        }

        if ($atoms === 0 && empty($elements)) {
            return 'raw:'.md5(preg_replace('/\s+/', ' ', trim($molfile)) ?? '');
        }

        ksort($elements);
        $hist = '';
        foreach ($elements as $sym => $count) {
            $hist .= $sym.$count;
        }

        return $atoms.':'.$bonds.':'.$hist;
    }
}
