<?php

namespace Tests\Unit;

use App\Models\Dataset;
use App\Models\Study;
use ReflectionMethod;
use Tests\TestCase;

class ResolvesNmrxivRouteBindingTest extends TestCase
{
    /**
     * @param  class-string  $modelClass
     * @return array{0: bool, 1: string}
     */
    private function matchesIdentifier(string $modelClass, string $value): bool
    {
        $method = new ReflectionMethod($modelClass, 'matchesNmrxivIdentifier');
        $method->setAccessible(true);

        return $method->invoke(null, $value);
    }

    public function test_study_matches_sample_identifiers(): void
    {
        $this->assertTrue($this->matchesIdentifier(Study::class, 'S7'));
        $this->assertTrue($this->matchesIdentifier(Study::class, 's7'));
        $this->assertTrue($this->matchesIdentifier(Study::class, 'NMRXIV:S7'));
        $this->assertFalse($this->matchesIdentifier(Study::class, 'D7'));
        $this->assertFalse($this->matchesIdentifier(Study::class, '7'));
        $this->assertFalse($this->matchesIdentifier(Study::class, 'P7'));
    }

    public function test_dataset_matches_dataset_identifiers(): void
    {
        $this->assertTrue($this->matchesIdentifier(Dataset::class, 'D9'));
        $this->assertTrue($this->matchesIdentifier(Dataset::class, 'd9'));
        $this->assertTrue($this->matchesIdentifier(Dataset::class, 'NMRXIV:D9'));
        $this->assertFalse($this->matchesIdentifier(Dataset::class, 'S9'));
        $this->assertFalse($this->matchesIdentifier(Dataset::class, '9'));
        $this->assertFalse($this->matchesIdentifier(Dataset::class, 'P9'));
    }
}
