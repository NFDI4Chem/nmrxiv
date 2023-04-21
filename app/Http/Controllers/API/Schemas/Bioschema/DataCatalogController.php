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

        $confromsTo = $creativeWork;

        $keywords = $this->prepareKeywords();
        $contributors = $this->prepareContributors();

        $nmrXivProvider = Schema::organization();
        $nmrXivProvider->name(env('NMRXIV_PROVIDER'));
        $nmrXivProvider->url(env('NMRXIV_PROVIDER_URL'));

        $dataCatalog = Schema::dataCatalog();
        $dataCatalog['@id'] = url(env('APP_URL'));
        $dataCatalog['dct:conformsTo'] = $confromsTo;
        $dataCatalog->description(env('APP_DESCRIPTION'));
        $dataCatalog->keywords($keywords);
        $dataCatalog->name(env('APP_NAME'));
        $dataCatalog->provider($nmrXivProvider);
        $dataCatalog->url(env('APP_URL'));
        $dataCatalog->identifier(env('APP_URL'));
        $dataCatalog->contributor($contributors);

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
        $pulsedNMR = $this->getDefinedTerm('pulsed nuclear magnetic resonance spectroscopy', ['NMR', 'nuclear magnetic resonance spectroscopy', 'NMR spectroscopy'], 'CHMO:0000613', 'https://ontobee.org/ontology/CHMO?iri=http://purl.obolibrary.org/obo/CHMO_0000613', $chmo);
        $nmr = $this->getDefinedTerm('NMR', ['Nuclear magnetic resonance spectroscopy', 'NMR spectroscopy'], 'topic:0593', 'https://bioportal.bioontology.org/ontologies/EDAM?p=classes&conceptid=topic_0593', $edam);
        $oneDNMR = $this->getDefinedTerm('one-dimensional nuclear magnetic resonance spectroscopy', ['1D NMR spectroscopy', '1-D NMR', 'one-dimensional nuclear magnetic resonance spectroscopy', '1D NMR', '1D nuclear magnetic resonance spectroscopy'], 'CHMO:0000592', 'http://purl.obolibrary.org/obo/CHMO_0000592', $chmo);
        $twoDNMR = $this->getDefinedTerm('two-dimensional nuclear magnetic resonance spectroscopy', ['2-D NMR', '2D NMR', 'two-dimensional nuclear magnetic resonance spectroscopy', '2D NMR spectroscopy', 'two-dimensional NMR', '2D nuclear magnetic resonance'], 'CHMO:0000598', 'http://purl.obolibrary.org/obo/CHMO_0000598', $chmo);
        $cosy = $this->getDefinedTerm('correlation spectroscopy spectrum', ['COSY spectra', 'COSY spectrum', 'COSY NMR spectrum', 'COSY NMR spectra'], 'CHMO:0002450', 'http://purl.obolibrary.org/obo/CHMO_0002450', $chmo);
        $hsqc = $this->getDefinedTerm('heteronuclear single quantum coherence', ['HSQC'], 'CHMO:0000604', 'http://purl.obolibrary.org/obo/CHMO_0000604', $chmo);
        $hmbc = $this->getDefinedTerm('heteronuclear multiple bond coherence', ['HMBC NMR', 'HMBC'], 'CHMO:0000601', 'http://purl.obolibrary.org/obo/CHMO_0000601', $chmo);
        $noesy = $this->getDefinedTerm('two-dimensional nuclear Overhauser enhancement spectrum', ['2D NOESY-NMR spectrum', '2D NOESY-NMR spectra', '2D NOESY spectra', '2D NOESY spectrum'], 'CHMO:0001171', 'http://purl.obolibrary.org/obo/CHMO_0001171', $chmo);
        $brukerNMR = $this->getDefinedTerm('Bruker', [], 'NMR:1400256', 'http://nmrML.org/nmrCV#NMR:1400256', $nmrcv);
        $joel = $this->getDefinedTerm('JEOL', [], 'NMR:1400258', 'http://nmrML.org/nmrCV#NMR:1400258', $nmrcv);
        $nmreData = $this->getDefinedTerm('NMReDATA', [], 'format:3906', 'http://edamontology.org/format_3906', $nmrcv);

        $keywords = [$pulsedNMR, $nmr, $oneDNMR, $twoDNMR, $cosy, $hsqc, $hmbc, $noesy, $brukerNMR, $joel, $nmreData];

        return $keywords;
    }

    /**
     * Get Person from given and family names.
     *
     *
     * @param  string  $givenName
     * @param  string  $familyName
     * @return object $Person
     */
    public function getPerson($givenName, $familyName)
    {
        $contributor = Schema::Person();
        $contributor->givenName($givenName);
        $contributor->familyName($familyName);

        return $contributor;
    }

    /**
     * Prepare Contributors.
     *
     * @param null
     * @return array $contributors
     */
    public function prepareContributors()
    {
        $Annett = $this->getPerson('Annett', 'Schröter');
        $Christian = $this->getPerson('Christian', 'Popp');
        $Christoph = $this->getPerson('Christoph', 'Steinbeck');
        $Darina = $this->getPerson('Darina', 'Storozhuk');
        $David = $this->getPerson('David', 'Rauh');
        $Guido = $this->getPerson('Guido', 'Pauli');
        $Hamed = $this->getPerson('Hamed', 'Musallam');
        $Johannes = $this->getPerson('Johannes', 'Liermann');
        $Julien = $this->getPerson('Julien', 'Wist');
        $Kohulan = $this->getPerson('Kohulan', 'Rajan');
        $Luc = $this->getPerson('Luc', 'Patiny');
        $Markus = $this->getPerson('Markus', 'Lange');
        $Nazar = $this->getPerson('Nazar', 'Stefaniuk');
        $Nils = $this->getPerson('Nils', 'Schlörer');
        $Nisha = $this->getPerson('Nisha', 'Sharma');
        $Noura = $this->getPerson('Noura', 'Rayya');
        $Pascal = $this->getPerson('Pascal', 'Scherreiks');
        $Stephan = $this->getPerson('Stephan', 'Kuhn');
        $Steffen = $this->getPerson('Steffen', 'Neumann');
        $Tillmann = $this->getPerson('Tillmann', 'Fischer');
        $Venkata = $this->getPerson('Venkata Chandrasekhar', 'Nainala');

        $contributors = [$Annett, $Christian, $Christoph, $Darina, $Guido, $Hamed, $Johannes, $Julien, $Kohulan, $Luc, $Markus, $Nazar, $Nils, $Nisha, $Noura, $Pascal, $Stephan, $Steffen, $Tillmann, $Venkata];

        return $contributors;
    }
}
