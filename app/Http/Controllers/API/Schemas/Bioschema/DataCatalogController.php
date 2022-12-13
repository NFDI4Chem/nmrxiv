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

        
        $chmo = Schema:: DefinedTermSet ();
        $chmo->name('Chemical Methods Ontology');
        $chmo->url('http://purl.obolibrary.org/obo/chmo.owl');

        $edam = Schema:: DefinedTermSet ();
        $edam->name('Bioinformatics operations, data types, formats, identifiers and topics');
        $edam->url('http://edamontology.org');

        $pulsedNmr = Schema::DefinedTerm();
        $pulsedNmr->name('pulsed nuclear magnetic resonance spectroscopy');
        $pulsedNmr->identifier('CHMO:0000613');
        $pulsedNmr->url('https://ontobee.org/ontology/CHMO?iri=http://purl.obolibrary.org/obo/CHMO_0000613');
        $pulsedNmr->inDefinedTermSet($chmo);

        $nmr = Schema::DefinedTerm();
        $nmr->name('NMR');
        $nmr->identifier('topic:0593');
        $nmr->url('https://bioportal.bioontology.org/ontologies/EDAM?p=classes&conceptid=topic_0593');
        $nmr->inDefinedTermSet($edam);

        $keywords = [$pulsedNmr,$nmr];

        $nmrXivProvider = Schema::organization();
        $nmrXivProvider->name(env('NMRXIV_PROVIDER')); 
        $nmrXivProvider->url(env('NMRXIV_PROVIDER_URL')); 

        $dataCatalog = Schema::dataCatalog();
        $dataCatalog['@id'] = url(env('APP_URL'));
        $dataCatalog['dct:conformsTo'] = $confromsTo;
        $dataCatalog->description(env('APP_DESCRIPTION'));
        $dataCatalog->keywords($keywords);
        #$dataCatalog->keywords(['NMR', 'Nuclear Magnetic Resonance Spectroscopy', 'FAIR NMR', '1D NMR', '2D NMR', 'COSY', 'HSQC', 'HMBC', 'NOESY', 'Sepctral raw data', 'Bruker NMR', 'JOEL', 'NMReData', 'NMRium']);
        $dataCatalog->name(env('APP_NAME'));
        $dataCatalog->provider($nmrXivProvider);
        $dataCatalog->url(env('APP_URL'));

        $dataCatalog->measurementTechnique(env('MEASUREMENT_TECHNIQUE'));

        return $dataCatalog;
    }
}
