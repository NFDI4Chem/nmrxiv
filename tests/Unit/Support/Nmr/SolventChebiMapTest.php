<?php

namespace Tests\Unit\Support\Nmr;

use App\Support\Nmr\SolventChebiMap;
use PHPUnit\Framework\TestCase;

class SolventChebiMapTest extends TestCase
{
    public function test_known_solvents_resolve_to_chebi_iris(): void
    {
        $this->assertSame(
            'http://purl.obolibrary.org/obo/CHEBI_85365',
            SolventChebiMap::lookup('CDCl3')
        );

        $this->assertSame(
            'http://purl.obolibrary.org/obo/CHEBI_193041',
            SolventChebiMap::lookup('DMSO-d6')
        );

        $this->assertSame(
            'http://purl.obolibrary.org/obo/CHEBI_41981',
            SolventChebiMap::lookup('D2O')
        );

        $this->assertSame(
            'http://purl.obolibrary.org/obo/CHEBI_85369',
            SolventChebiMap::lookup('CD3OD')
        );
    }

    public function test_normalization_tolerates_whitespace_separators_and_subscripts(): void
    {
        $iri = 'http://purl.obolibrary.org/obo/CHEBI_193041';
        $this->assertSame($iri, SolventChebiMap::lookup('DMSO-d6'));
        $this->assertSame($iri, SolventChebiMap::lookup('dmso d6'));
        $this->assertSame($iri, SolventChebiMap::lookup('DMSO_d6'));
        $this->assertSame($iri, SolventChebiMap::lookup('DMSO.d6'));
        $this->assertSame($iri, SolventChebiMap::lookup('dmso-d₆'));
    }

    public function test_unknown_solvent_returns_null(): void
    {
        $this->assertNull(SolventChebiMap::lookup('made-up-solvent'));
        $this->assertNull(SolventChebiMap::lookup(''));
        $this->assertNull(SolventChebiMap::lookup(null));
    }
}
