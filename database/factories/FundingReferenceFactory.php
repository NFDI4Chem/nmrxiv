<?php

namespace Database\Factories;

use App\Models\FundingReference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundingReference>
 */
class FundingReferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'funder_name' => 'Deutsche Forschungsgemeinschaft',
            'funder_identifier' => 'https://ror.org/018mejw64',
            'funder_identifier_type' => 'ROR',
            'award_number' => '441958208',
            'award_title' => 'NFDI4Chem – Chemistry Consortium in the NFDI',
            'award_uri' => 'https://gepris.dfg.de/gepris/projekt/441958208',
        ];
    }
}
