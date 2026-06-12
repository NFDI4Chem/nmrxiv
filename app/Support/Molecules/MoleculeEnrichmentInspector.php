<?php

namespace App\Support\Molecules;

use App\Models\Molecule;
use Illuminate\Database\Eloquent\Builder;

/**
 * Detects molecules that {@see SanitizeMolecules} can still enrich.
 */
class MoleculeEnrichmentInspector
{
    public static function needsEnrichment(Molecule $molecule): bool
    {
        if ($molecule->standard_inchi && (
            empty($molecule->iupac_name)
            || empty($molecule->molecular_formula)
            || empty($molecule->molecular_weight)
        )) {
            return true;
        }

        if (! $molecule->canonical_smiles && $molecule->sdf) {
            return true;
        }

        if ($molecule->canonical_smiles && ! $molecule->cas && config('services.cas.api_token')) {
            return true;
        }

        return false;
    }

    /**
     * Text columns such as molecular_formula are NOT NULL in the schema and
     * are stored as empty strings when unknown, so "missing" must cover both
     * NULL and '' (mirroring the empty() checks in needsEnrichment()).
     *
     * @return Builder<Molecule>
     */
    public static function needingEnrichmentQuery(): Builder
    {
        return Molecule::query()->where(function (Builder $query) {
            $query->where(function (Builder $inner) {
                $inner->whereNotNull('standard_inchi')
                    ->where('standard_inchi', '!=', '')
                    ->where(function (Builder $fields) {
                        $fields->whereNull('iupac_name')
                            ->orWhere('iupac_name', '')
                            ->orWhereNull('molecular_formula')
                            ->orWhere('molecular_formula', '')
                            ->orWhereNull('molecular_weight');
                    });
            })->orWhere(function (Builder $inner) {
                $inner->whereNotNull('sdf')
                    ->where('sdf', '!=', '')
                    ->where(function (Builder $smiles) {
                        $smiles->whereNull('canonical_smiles')
                            ->orWhere('canonical_smiles', '');
                    });
            });

            if (config('services.cas.api_token')) {
                $query->orWhere(function (Builder $inner) {
                    $inner->whereNotNull('canonical_smiles')
                        ->where('canonical_smiles', '!=', '')
                        ->where(function (Builder $cas) {
                            $cas->whereNull('cas')->orWhere('cas', '');
                        });
                });
            }
        });
    }
}
