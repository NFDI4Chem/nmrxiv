<?php

namespace App\Enums;

/**
 * Spectrum comparison modes, after nmrshiftdb2 section 2.3.1.
 *
 * Contains: every query peak is looked for in the record; extra record peaks
 * are ignored (nmrshiftdb "subspectrum search").
 * Whole: unmatched peaks on either side lower the score (nmrshiftdb
 * "complete spectrum search").
 */
enum SpectrumSearchMode: string
{
    case Contains = 'contains';
    case Whole = 'whole';
}
