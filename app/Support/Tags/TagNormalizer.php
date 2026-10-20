<?php

namespace App\Support\Tags;

use Illuminate\Validation\ValidationException;

/**
 * Cleans user-supplied keywords before they are stored as tags.
 *
 * The production database uses the SQL_ASCII encoding, which cannot translate
 * non-ASCII characters stored as JSON unicode escapes (e.g. "•"). A single
 * such tag makes every later tag lookup fail with a 500, so keywords containing
 * them are rejected with a validation error instead.
 */
class TagNormalizer
{
    /**
     * Characters used to separate keywords in pasted lists.
     */
    private const SEPARATORS = '/[|\r\n]+/';

    /**
     * @param  mixed  $tags  Keywords as submitted (array of strings or a single string)
     * @param  string  $field  Validation error key shown to the user
     * @return array<int, string>
     *
     * @throws ValidationException
     */
    public static function normalize(mixed $tags, string $field = 'tags'): array
    {
        $list = [];

        foreach (is_array($tags) ? $tags : [$tags] as $tag) {
            if (! is_string($tag)) {
                continue;
            }

            $parts = preg_split(self::SEPARATORS, $tag) ?: [];

            foreach ($parts as $part) {
                $part = trim($part);

                if ($part !== '') {
                    $list[] = $part;
                }
            }
        }

        $list = array_values(array_unique($list));

        $unsupported = array_values(array_filter($list, fn (string $tag) => preg_match('/[^\x20-\x7E]/', $tag) === 1));

        if ($unsupported !== []) {
            throw ValidationException::withMessages([
                $field => 'These keywords contain unsupported characters: '.implode(', ', $unsupported)
                    .'. Please use plain letters, digits and basic punctuation (e.g. "alpha" instead of "α"),'
                    .' and separate keywords only with a comma (,), semicolon (;) or pipe (|).',
            ]);
        }

        return $list;
    }
}
