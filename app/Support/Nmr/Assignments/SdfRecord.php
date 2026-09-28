<?php

namespace App\Support\Nmr\Assignments;

/**
 * First record of an SD file: the molfile block and its `> <TAG>` data items.
 */
final readonly class SdfRecord
{
    /**
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public string $molfile,
        public array $tags,
    ) {}

    public static function parse(string $contents): self
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $contents);
        $record = explode("\n\$\$\$\$", $normalized, 2)[0];

        $end = preg_match('/^M {2}END[^\n]*$/m', $record, $match, PREG_OFFSET_CAPTURE);
        if ($end !== 1) {
            throw new InvalidAssignmentFileException('The file does not contain a molfile (no "M  END" line).');
        }

        $molfileEnd = $match[0][1] + strlen($match[0][0]);
        $molfile = substr($record, 0, $molfileEnd)."\n";

        return new self($molfile, self::parseTags(substr($record, $molfileEnd)));
    }

    public function tag(string $name): ?string
    {
        return $this->tags[strtoupper($name)] ?? null;
    }

    /**
     * @return list<string>
     */
    public function tagLines(string $name): array
    {
        $value = $this->tag($name);
        if ($value === null) {
            return [];
        }

        return array_values(array_filter(
            array_map(fn (string $line): string => rtrim(trim($line), '\\'), explode("\n", $value)),
            fn (string $line): bool => trim($line) !== '',
        ));
    }

    /**
     * Author atom labels from Mnova's `M  ZZC` lines, keyed by atom index.
     *
     * @return array<int, string>
     */
    public function atomLabels(): array
    {
        preg_match_all('/^M {2}ZZC\s+(\d+)\s+(\S+)\s*$/m', $this->molfile, $matches, PREG_SET_ORDER);

        $labels = [];
        foreach ($matches as $match) {
            $labels[(int) $match[1]] = $match[2];
        }

        return $labels;
    }

    /**
     * Element symbols keyed by 1-based atom index (V2000 only).
     *
     * @return array<int, string>
     */
    public function atomSymbols(): array
    {
        $lines = explode("\n", $this->molfile);
        $countsLine = $lines[3] ?? '';
        if (! str_contains($countsLine, 'V2000')) {
            return [];
        }

        $atomCount = (int) substr($countsLine, 0, 3);
        $symbols = [];
        for ($index = 1; $index <= $atomCount; $index++) {
            $symbols[$index] = trim(substr($lines[3 + $index] ?? '', 31, 3));
        }

        return $symbols;
    }

    /**
     * @return array<string, string>
     */
    private static function parseTags(string $block): array
    {
        $tags = [];
        $current = null;

        foreach (explode("\n", $block) as $line) {
            if (preg_match('/^>\s*.*<([^>]+)>/', $line, $match) === 1) {
                $current = strtoupper(trim($match[1]));
                $tags[$current] = '';

                continue;
            }

            if ($current === null) {
                continue;
            }

            if (trim($line) === '') {
                $current = null;

                continue;
            }

            $tags[$current] .= ($tags[$current] === '' ? '' : "\n").$line;
        }

        return $tags;
    }
}
