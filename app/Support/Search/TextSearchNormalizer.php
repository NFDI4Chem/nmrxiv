<?php

namespace App\Support\Search;

class TextSearchNormalizer
{
    public static function sanitize(?string $query): ?string
    {
        if ($query === null || $query === '') {
            return null;
        }

        $query = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $query) ?? '';
        $query = trim($query);
        $query = substr($query, 0, 1000);

        return $query === '' ? null : $query;
    }

    public static function normalize(?string $query): ?string
    {
        $query = self::sanitize($query);

        if ($query === null) {
            return null;
        }

        $query = preg_replace('/\s+/u', ' ', $query) ?? '';
        $query = preg_replace('/[-_\/]+/u', ' ', $query) ?? '';
        $query = preg_replace('/\s+/u', ' ', trim($query)) ?? '';

        if ($query === '') {
            return null;
        }

        return function_exists('mb_strtolower')
            ? mb_strtolower($query)
            : strtolower($query);
    }

    /**
     * @return list<string>
     */
    public static function tokens(?string $query): array
    {
        $normalized = self::normalize($query);

        if ($normalized === null || $normalized === '') {
            return [];
        }

        return array_values(array_filter(
            explode(' ', $normalized),
            static fn (string $token): bool => $token !== ''
        ));
    }
}
