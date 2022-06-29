<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\License;
use Inertia\Inertia;

class LicenseController extends Controller
{
    /**
     * Return All Licenses
     */
    public function index(Request $request)
    {
        $licenses = Cache::rememberForever('licenses', function (){
            return License::select('id','title', 'description')->get();
        });

       // $licenses = License::select('id','title')->get();
        return $licenses;
    }

    /**
     * Return License for particular ID.
     */
    public function getLicensebyId(Request $request, $id)
    {   
        $license = License::select('id','title','description')->where('id', $id)->get();
        return $license;
    }


}
