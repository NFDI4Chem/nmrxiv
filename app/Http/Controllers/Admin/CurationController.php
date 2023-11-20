<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Inertia\Inertia;

class CurationController extends Controller
{
    public function spectra()
    {
        return Inertia::render('Console/Spectra/Index');
    }

    public function snapshots()
    {
        return Inertia::render('Console/Spectra/Snapshot', [
            'datasets' => Dataset::where('has_nmrium', 1)->where('dataset_photo_path', null)->pluck('id')->all(),
        ]);
    }
}
