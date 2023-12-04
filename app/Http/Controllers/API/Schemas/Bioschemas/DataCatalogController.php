<?php

namespace App\Http\Controllers\API\Schemas\Bioschemas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;
use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;

/**
 * Implement Bioschemas Data Catalog type on nmrXiv as a repository to enable exporting
 * its metadata with a json endpoint.
 */
class DataCatalogController extends Controller
{
    /**
     * Implement Bioschemas Data Catalog type on nmrXiv as a repository.
     *
     * @link https://bioschemas.org/profiles/DataCatalog/0.3-RELEASE-2019_07_01
     *
     * @param  Illuminate\Http\Request  $request
     * @return object $dataCatalog
     */
    public function dataCatalogSchema(Request $request)
    {
        $creativeWork = Schema::CreativeWork();
        $creativeWork['@id'] = 'https://schema.org/DataCatalog"';

        $confromsTo = $creativeWork;

        $keywords = $this->prepareKeywords();
        $contributors = $this->prepareContributors();

        $nmrXivProvider = Schema::Organization();
        $nmrXivProvider->name(env('NMRXIV_PROVIDER'));
        $nmrXivProvider->url(env('NMRXIV_PROVIDER_URL'));

        $dataCatalog = Schema::DataCatalog();
        $dataCatalog['@id'] = url(env('APP_URL'));
        $dataCatalog['dct:conformsTo'] = $confromsTo;
        $dataCatalog->description(env('APP_DESCRIPTION'));
        $dataCatalog->keywords($keywords);
        $dataCatalog->name(env('APP_NAME'));
        $dataCatalog->provider($nmrXivProvider);
        $dataCatalog->url(env('APP_URL'));
        $dataCatalog->identifier(env('APP_URL'));
        $dataCatalog->license('https://mit-license.org/');
        $dataCatalog->contributor($contributors);
        $dataCatalog->isAccessibleForFree(true);

        $dataCatalog->measurementTechnique(env('MEASUREMENT_TECHNIQUE'));

        return $dataCatalog;
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
        $chmo = BioschemasHelper::prepareDefinedTermSet('Chemical Methods Ontology', 'http://purl.obolibrary.org/obo/chmo.owl');
        $nmrcv = BioschemasHelper::prepareDefinedTermSet('nuclear magnetic resonance CV', 'http://nmrml.org/cv/');

        //Prepare Defined Term
        $nmr = BioschemasHelper::prepareDefinedTerm('nuclear magnetic resonance spectroscopy', ['NMR', 'NMR spectroscopy', 'nuclear magnetic resonance (NMR) spectroscopy'], 'CHMO:0000591', 'http://purl.obolibrary.org/obo/CHMO_0000591', $chmo);
        $pulsedNMR = BioschemasHelper::prepareDefinedTerm('pulsed nuclear magnetic resonance spectroscopy', ['NMR', 'nuclear magnetic resonance spectroscopy', 'NMR spectroscopy'], 'CHMO:0000613', 'https://ontobee.org/ontology/CHMO?iri=http://purl.obolibrary.org/obo/CHMO_0000613', $chmo);
        $oneDNMR = BioschemasHelper::prepareDefinedTerm('one-dimensional nuclear magnetic resonance spectroscopy', ['1D NMR spectroscopy', '1-D NMR', 'one-dimensional nuclear magnetic resonance spectroscopy', '1D NMR', '1D nuclear magnetic resonance spectroscopy'], 'CHMO:0000592', 'http://purl.obolibrary.org/obo/CHMO_0000592', $chmo);
        $twoDNMR = BioschemasHelper::prepareDefinedTerm('two-dimensional nuclear magnetic resonance spectroscopy', ['2-D NMR', '2D NMR', 'two-dimensional nuclear magnetic resonance spectroscopy', '2D NMR spectroscopy', 'two-dimensional NMR', '2D nuclear magnetic resonance'], 'CHMO:0000598', 'http://purl.obolibrary.org/obo/CHMO_0000598', $chmo);
        $cosy = BioschemasHelper::prepareDefinedTerm('correlation spectroscopy spectrum', ['COSY spectra', 'COSY spectrum', 'COSY NMR spectrum', 'COSY NMR spectra'], 'CHMO:0002450', 'http://purl.obolibrary.org/obo/CHMO_0002450', $chmo);
        $hsqc = BioschemasHelper::prepareDefinedTerm('heteronuclear single quantum coherence', ['HSQC'], 'CHMO:0000604', 'http://purl.obolibrary.org/obo/CHMO_0000604', $chmo);
        $hmbc = BioschemasHelper::prepareDefinedTerm('heteronuclear multiple bond coherence', ['HMBC NMR', 'HMBC'], 'CHMO:0000601', 'http://purl.obolibrary.org/obo/CHMO_0000601', $chmo);
        $noesy = BioschemasHelper::prepareDefinedTerm('two-dimensional nuclear Overhauser enhancement spectrum', ['2D NOESY-NMR spectrum', '2D NOESY-NMR spectra', '2D NOESY spectra', '2D NOESY spectrum'], 'CHMO:0001171', 'http://purl.obolibrary.org/obo/CHMO_0001171', $chmo);
        $brukerNMR = BioschemasHelper::prepareDefinedTerm('Bruker', [], 'NMR:1400256', 'http://nmrML.org/nmrCV#NMR:1400256', $nmrcv);
        $joel = BioschemasHelper::prepareDefinedTerm('JEOL', [], 'NMR:1400258', 'http://nmrML.org/nmrCV#NMR:1400258', $nmrcv);
        $nmreData = BioschemasHelper::prepareDefinedTerm('NMReDATA', [], 'format:3906', 'http://edamontology.org/format_3906', $nmrcv);

        $keywords = [$nmr, $pulsedNMR, $oneDNMR, $twoDNMR, $cosy, $hsqc, $hmbc, $noesy, $brukerNMR, $joel, $nmreData];

        return $keywords;
    }

    /**
     * Prepare Contributors.
     *
     * @param null
     * @return array $contributors
     */
    public function prepareContributors()
    {
        $Annett = BioschemasHelper::preparePerson('0000-0002-2542-0867', 'Annett', 'Schröter');
        $Christian = BioschemasHelper::preparePerson(null, 'Christian', 'Popp');
        $Christoph = BioschemasHelper::preparePerson('0000-0001-6966-0814', 'Christoph', 'Steinbeck');
        $Darina = BioschemasHelper::preparePerson(null, 'Darina', 'Storozhuk');
        $David = BioschemasHelper::preparePerson('0000-0001-7499-1693', 'David', 'Rauh');
        $Guido = BioschemasHelper::preparePerson('0000-0003-1022-4326', 'Guido', 'Pauli');
        $Hamed = BioschemasHelper::preparePerson(null, 'Hamed', 'Musallam');
        $Johannes = BioschemasHelper::preparePerson('0000-0003-2060-842X', 'Johannes', 'Liermann');
        $Julien = BioschemasHelper::preparePerson('0000-0002-3416-2572', 'Julien', 'Wist');
        $Kohulan = BioschemasHelper::preparePerson('0000-0003-1066-7792', 'Kohulan', 'Rajan');
        $Luc = BioschemasHelper::preparePerson('0000-0002-4943-2643', 'Luc', 'Patiny');
        $Markus = BioschemasHelper::preparePerson(null, 'Markus', 'Lange');
        $Nazar = BioschemasHelper::preparePerson('0000-0002-5870-8496', 'Nazar', 'Stefaniuk');
        $Nils = BioschemasHelper::preparePerson('0000-0002-0990-9582', 'Nils', 'Schlörer');
        $Nisha = BioschemasHelper::preparePerson('0009-0006-4755-1039', 'Nisha', 'Sharma');
        $Noura = BioschemasHelper::preparePerson('0009-0001-5998-5030', 'Noura', 'Rayya');
        $Pascal = BioschemasHelper::preparePerson(null, 'Pascal', 'Scherreiks');
        $Stefan = BioschemasHelper::preparePerson('0000-0002-5990-4157', 'Stefan', 'Kuhn');
        $Steffen = BioschemasHelper::preparePerson('0000-0002-7899-7192', 'Steffen', 'Neumann');
        $Tillmann = BioschemasHelper::preparePerson('0000-0003-4480-8661', 'Tillmann', 'Fischer');
        $Venkata = BioschemasHelper::preparePerson('0000-0002-2564-3243', 'Venkata Chandrasekhar', 'Nainala');

        $contributors = [$Annett, $Christian, $Christoph, $Darina, $Guido, $Hamed, $Johannes, $Julien, $Kohulan, $Luc, $Markus, $Nazar, $Nils, $Nisha, $Noura, $Pascal, $Stefan, $Steffen, $Tillmann, $Venkata];

        return $contributors;
    }
}
