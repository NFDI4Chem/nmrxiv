<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ResolvesNmrxivRouteBinding
{
    /**
     * @return class-string
     */
    abstract protected static function nmrxivRouteBindingNamespace(): string;

    abstract protected static function nmrxivRouteBindingPrefix(): string;

    /**
     * Resolve route bindings from numeric ids or NMRXIV identifiers (e.g. S123, D456).
     *
     * @param  mixed  $value
     * @param  string|null  $field
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if (is_string($value) && static::matchesNmrxivIdentifier($value)) {
            $resolved = resolveIdentifier($value);

            if (($resolved['namespace'] ?? null) === static::nmrxivRouteBindingNamespace()
                && $resolved['model'] !== null
            ) {
                return $resolved['model'];
            }

            throw (new ModelNotFoundException)->setModel(static::class, [$value]);
        }

        return parent::resolveRouteBinding($value, $field);
    }

    protected static function matchesNmrxivIdentifier(string $value): bool
    {
        $prefix = static::nmrxivRouteBindingPrefix();

        return (bool) preg_match('/^(NMRXIV:)?['.$prefix.$prefix.'][0-9]+$/i', $value);
    }
}
