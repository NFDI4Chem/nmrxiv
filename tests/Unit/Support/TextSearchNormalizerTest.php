<?php

namespace Tests\Unit\Support;

use App\Support\Search\TextSearchNormalizer;
use PHPUnit\Framework\TestCase;

class TextSearchNormalizerTest extends TestCase
{
    public function test_normalize_collapses_whitespace_and_lowercases(): void
    {
        $this->assertSame('caffeine study', TextSearchNormalizer::normalize('  Caffeine   Study  '));
    }

    public function test_tokens_split_on_whitespace_after_punctuation_normalization(): void
    {
        $this->assertSame(['caffeine', 'nmr'], TextSearchNormalizer::tokens('caffeine-NMR'));
    }

    public function test_tokens_returns_empty_for_blank_input(): void
    {
        $this->assertSame([], TextSearchNormalizer::tokens('   '));
    }
}
