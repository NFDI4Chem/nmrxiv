<?php

namespace Tests\Unit\Support;

use App\Models\Draft;
use App\Support\ProvisionalDoi;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ProvisionalDoiTest extends TestCase
{
    public function test_it_builds_placeholder_from_draft_key_segment(): void
    {
        Config::set('doi.datacite.prefix', '10.88888');

        $draft = new Draft(['key' => 'deadbeef-0000-0000-0000-000000000001']);

        $this->assertSame('10.88888/nmrxiv.deadbeef', ProvisionalDoi::forDraft($draft));
    }

    public function test_it_strips_leading_datacite_slash_from_prefix(): void
    {
        Config::set('doi.datacite.prefix', 'datacite/10.88888');

        $draft = new Draft(['key' => 'abcd-efgh-ijkl']);

        $this->assertSame('10.88888/nmrxiv.abcd', ProvisionalDoi::forDraft($draft));
    }
}
