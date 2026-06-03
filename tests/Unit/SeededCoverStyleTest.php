<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class SeededCoverStyleTest extends TestCase
{
    public function test_seeded_cover_style_script_is_deterministic(): void
    {
        $result = Process::path(base_path())->run(['node', 'scripts/test-seeded-cover.mjs']);

        $this->assertTrue($result->successful(), $result->errorOutput());
        $this->assertStringContainsString('ok', $result->output());
    }
}
