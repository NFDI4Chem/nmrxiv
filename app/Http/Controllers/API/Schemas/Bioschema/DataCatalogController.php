<?php

namespace App\Http\Controllers\API\Schemas\Bioschema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;

/**
 * Implement Bioschema Data Catalog type on nmrXiv as a repository to enable exporting
 * its metadata with a json endpoint.
 */
class DataCatalogController extends Controller
{
    /**
     * Implement Bioschema Data Catalog type on nmrXiv as a repository.
     *
     * @link https://bioschemas.org/profiles/DataCatalog/0.3-RELEASE-2019_07_01
     *
     * @param  Illuminate\Http\Request  $request
     * @return object $dataCatalog
     */
    public function dataCatalogSchema(Request $request)
    {
        $creativeWork = Schema::creativeWork();
        $creativeWork['@id'] = 'https://bioschemas.org/profiles/DataCatalog/0.3-RELEASE-2019_07_01';

        $confromsTo = [];
        $confromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWork;

        $nmrXivProvider = Schema::organization();
        $nmrXivProvider->name('NFDI4Chem');
        $nmrXivProvider->url('https://www.nfdi4chem.de/');

        $dataCatalog = Schema::dataCatalog();
        $dataCatalog['@id'] = url('https://nmrxiv.org');
        $dataCatalog['dct:conformsTo'] = $confromsTo;
        $dataCatalog->description(env('APP_DESCRIPTION'), 'nmrXiv is currently developed as the FAIR, consensus-driven NMR data repository and computational platform. The ultimate goal is to accelerate broader coordination and data sharing among natural product (NP) researchers by enabling the storage, management, sharing and analysis of NMR data.');
        $dataCatalog->keywords(['NMR', 'Nuclear Magnetic Resonance Spectroscopy', 'FAIR NMR', '1D NMR', '2D NMR', 'COSY', 'HSQC', 'HMBC', 'NOESY', 'Sepctral raw data', 'Bruker NMR', 'JOEL', 'NMReData', 'NMRium']);
        $dataCatalog->name(env('APP_NAME'));
        $dataCatalog->provider($nmrXivProvider);
        $dataCatalog->url('https://nmrxiv.org');

        $dataCatalog->measurementTechnique('https://www.ebi.ac.uk/ols/ontologies/chmo/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FCHMO_0000591');

        return $dataCatalog;
    }
}
