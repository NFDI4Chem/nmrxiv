<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LicenseController extends Controller
{
    /**
     * Return All Licenses
     */
    public function index(Request $request)
    {
        $licenses = Cache::rememberForever('licenses', function () {
            return License::select('id', 'title', 'description')->orderBy('id', 'ASC')->get();
        });

        return $licenses;
    }
}
