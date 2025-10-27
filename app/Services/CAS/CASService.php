<?php

namespace App\Services\CAS;

interface CASService
{
    /**
     * Fetch chemical details by CAS Registry Number
     */
    public function getCASDetails(string $casNumber): array;

    /**
     * Search for CAS number by SMILES
     */
    public function searchCASBySmiles(string $smiles): ?string;
}
