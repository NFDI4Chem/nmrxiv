<?php

namespace App\Http\Controllers\API\Bioschema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;

class DataCatalogController extends Controller
{
    public function schema(Request $request)
    {
        $dataCatalog = Schema::dataCatalog();

        $nmrXivProvider = Schema::organization();
        $nmrXivProvider->name('NFDI4Chem');
        $nmrXivProvider->url('https://www.nfdi4chem.de/');

        $creativeWork = Schema::creativeWork();
        $creativeWork['@id'] = 'https://bioschemas.org/profiles/DataCatalog/0.3-RELEASE-2019_07_01';

        $confromsTo = [];
        $confromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWork;

        $dataCatalog['@id'] = url('https://nmrxiv.org');
        $dataCatalog['dct:conformsTo'] = $confromsTo;
        $dataCatalog->name(env('APP_NAME'));
        $dataCatalog->description(env('APP_DESCRIPTION', 'New, Open & FAIR, and consensus-driven NMR data repository and computational platform. The ultimate goal is to accelerate broader coordination and data sharing among natural product (NP) researchers by enabling storage, management, sharing and analysis of NMR data.'));
        $dataCatalog->keywords(['NMR', 'Nuclear Magnetic Resonance Spectroscopy', 'FAIR NMR', '1D NMR', '2D NMR', 'COSY', 'HSQC', 'HMBC', 'NOESY', 'Sepctral raw data', 'Bruker NMR', 'JOEL']);
        $dataCatalog->provider($nmrXivProvider);
        $dataCatalog->url('https://nmrxiv.org');

        return $dataCatalog;
    }
}
