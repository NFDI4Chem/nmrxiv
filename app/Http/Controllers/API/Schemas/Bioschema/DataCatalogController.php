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

        $keywords = $this->prepareKeywords();

        $nmrXivProvider = Schema::organization();
        $nmrXivProvider->name(env('NMRXIV_PROVIDER'));
        $nmrXivProvider->url(env('NMRXIV_PROVIDER_URL'));

        $dataCatalog = Schema::dataCatalog();
        $dataCatalog['@id'] = url(env('APP_URL'));
        $dataCatalog['dct:conformsTo'] = $confromsTo;
        $dataCatalog->description(env('APP_DESCRIPTION'));
        $dataCatalog->keywords($keywords);
        //$dataCatalog->keywords(['NMR', 'Nuclear Magnetic Resonance Spectroscopy', 'FAIR NMR', '1D NMR', '2D NMR', 'COSY', 'HSQC', 'HMBC', 'NOESY', 'Sepctral raw data', 'Bruker NMR', 'JOEL', 'NMReData', 'NMRium']);
        $dataCatalog->name(env('APP_NAME'));
        $dataCatalog->provider($nmrXivProvider);
        $dataCatalog->url(env('APP_URL'));

        $dataCatalog->measurementTechnique(env('MEASUREMENT_TECHNIQUE'));

        return $dataCatalog;
    }

    /**
     * Get Defined Term for ontology term.
     *
     *
     * @param  string  $name
     * @param  array  $alternameName
     * @param  string  $identifier
     * @param  string  $url
     * @param  object  $inDefinedTermSet
     * @return object $definedTerm
     */
    public function getDefinedTerm($name, $alternateName, $identifier, $url, $inDefinedTermSet)
    {
        $definedTerm = Schema::DefinedTerm();
        $definedTerm->name($name);
        $definedTerm->alternateName($alternateName);
        $definedTerm->identifier($identifier);
        $definedTerm->url($url);
        $definedTerm->inDefinedTermSet($inDefinedTermSet);

        return $definedTerm;
    }

    /**
     * Get Defined Term set for ontology service.
     *
     *
     * @param  string  $name
     * @param  string  $url
     * @return object $definedTermSet
     */
    public function getDefinedTermSet($name, $url)
    {
        $definedTermSet = Schema::DefinedTermSet();
        $definedTermSet->name($name);
        $definedTermSet->url($url);

        return $definedTermSet;
    }

    /**
     * Prepare Keywords.
     *
     * @param null
     * @return array $keywords
     */
    public function prepareKeywords()
    {
        //Prepare Defined Term Set
        $chmo = $this->getDefinedTermSet('Chemical Methods Ontology', 'http://purl.obolibrary.org/obo/chmo.owl');
        $edam = $this->getDefinedTermSet('Bioinformatics operations, data types, formats, identifiers and topics', 'http://edamontology.org');
        $nmrcv = $this->getDefinedTermSet('nuclear magnetic resonance CV', 'http://nmrml.org/cv/');

        //Prepare Defined Term
        $pulsedNMR = $this->getDefinedTerm('pulsed nuclear magnetic resonance spectroscopy', ['NMR', 'pulsed nuclear magnetic resonance spectrometry', 'nuclear magnetic resonance spectroscopy', 'NMR spectrometry', 'nuclear magnetic resonance spectrometry', 'NMR spectroscopy'], 'CHMO:0000613', 'https://ontobee.org/ontology/CHMO?iri=http://purl.obolibrary.org/obo/CHMO_0000613', $chmo);
        $nmr = $this->getDefinedTerm('NMR', ['Nuclear Overhauser Effect Spectroscopy', 'Rotational Frame Nuclear Overhauser Effect Spectroscopy', 'ROESY', 'Nuclear magnetic resonance spectroscopy', 'NOESY', 'NMR spectroscopy', 'Heteronuclear Overhauser Effect Spectroscopy', 'HOESY'], 'topic:0593', 'https://bioportal.bioontology.org/ontologies/EDAM?p=classes&conceptid=topic_0593', $edam);
        $oneDNMR = $this->getDefinedTerm('one-dimensional nuclear magnetic resonance spectroscopy', ['1D NMR spectroscopy', '1-D NMR', '1D nuclear magnetic resonance spectrometry', 'one-dimensional nuclear magnetic resonance spectroscopy', 'one-dimensional nuclear magnetic resonance spectrometry', '1D NMR', '1D nuclear magnetic resonance spectroscopy', '1D NMR spectrometry'], 'CHMO:0000592', 'http://purl.obolibrary.org/obo/CHMO_0000592', $chmo);
        $twoDNMR = $this->getDefinedTerm('two-dimensional nuclear magnetic resonance spectroscopy', ['2-D NMR', '2D NMR', 'two-dimensional nuclear magnetic resonance spectroscopy', '2D NMR spectroscopy', '2D nuclear magnetic resonance spectrometry', 'two-dimensional NMR', '2D NMR spectrometry', '2D nuclear magnetic resonance', 'two-dimensional nuclear magnetic resonance spectrometry'], 'CHMO:0000598', 'http://purl.obolibrary.org/obo/CHMO_0000598', $chmo);
        $cosy = $this->getDefinedTerm('correlation spectroscopy spectrum', ['COSY spectra', 'COSY spectrum', 'COSY NMR spectrum', 'COSY NMR spectra'], 'CHMO:0002450', 'http://purl.obolibrary.org/obo/CHMO_0002450', $chmo);
        $hsqc = $this->getDefinedTerm('heteronuclear single quantum coherence', ['HSQC'], 'CHMO:0000604', 'http://purl.obolibrary.org/obo/CHMO_0000604', $chmo);
        $hmbc = $this->getDefinedTerm('heteronuclear multiple bond coherence', ['HMBC NMR', 'HMBC'], 'CHMO:0000601', 'http://purl.obolibrary.org/obo/CHMO_0000601', $chmo);
        $noesy = $this->getDefinedTerm('two-dimensional nuclear Overhauser enhancement spectrum', ['2D NOESY-NMR spectrum', '2D NOESY-NMR spectra', '2D NOESY spectra', '2D NOESY spectrum'], 'CHMO:0001171', 'http://purl.obolibrary.org/obo/CHMO_0001171', $chmo);
        $spectralRawData = $this->getDefinedTerm('Raw NMR data', [], 'data_0938', 'http://edamontology.org/data_0938', $edam);
        $brukerNMR = $this->getDefinedTerm('Bruker', [], 'NMR:1400256', 'http://nmrML.org/nmrCV#NMR:1400256', $nmrcv);
        $joel = $this->getDefinedTerm('JEOL', [], 'NMR:1400258', 'http://nmrML.org/nmrCV#NMR:1400258', $nmrcv);
        $nmreData = $this->getDefinedTerm('NMReDATA', [], 'http://edamontology.org/format_3906', 'http://edamontology.org/format_3906', $nmrcv);

        $keywords = [$pulsedNMR, $nmr, $oneDNMR, $twoDNMR, $cosy, $hsqc, $hmbc, $noesy, $spectralRawData, $brukerNMR, $joel, $nmreData];

        return $keywords;
    }
}
