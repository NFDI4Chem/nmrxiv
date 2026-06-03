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
     * @return Builder<Molecule>
     */
    public static function needingEnrichmentQuery(): Builder
    {
        return Molecule::query()->where(function (Builder $query) {
            $query->where(function (Builder $inner) {
                $inner->whereNotNull('standard_inchi')
                    ->where(function (Builder $fields) {
                        $fields->whereNull('iupac_name')
                            ->orWhereNull('molecular_formula')
                            ->orWhereNull('molecular_weight');
                    });
            })->orWhere(function (Builder $inner) {
                $inner->whereNotNull('sdf')->whereNull('canonical_smiles');
            });

            if (config('services.cas.api_token')) {
                $query->orWhere(function (Builder $inner) {
                    $inner->whereNotNull('canonical_smiles')->whereNull('cas');
                });
            }
        });
    }
}
