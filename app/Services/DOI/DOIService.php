<?php

namespace App\Services\DOI;

interface DOIService
{
    public function getDOIs();

    public function createDOI($suffix, $attributes = []);

    public function getDOI($doi);

    public function updateDOI($doi, $attributes = []);

    public function deleteDOI($doi);

    public function getDOIActivity($doi);
}
