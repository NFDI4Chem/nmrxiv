<?php

namespace App\Http\Controllers\API\Schemas\Bioschemas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;
use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
 
/**
 * Use Schema.org DataCatalog type to represent nmrXiv as a repository. 
 */
class DataCatalogController extends Controller
{
    /**
     * Use Schema.org DataCatalog type to represent nmrXiv as a repository.
     *
     * @link https://schema.org/DataCatalog
     *
     * @param  Illuminate\Http\Request  $request
     * @return object $dataCatalog
     */
    public function dataCatalogSchema(Request $request)
    {
        $keywords = $this->prepareKeywords();
        $contributors = $this->prepareContributors();

        $nmrXivProvider = Schema::Organization();
        $nmrXivProvider->name(env('NMRXIV_PROVIDER'));
        $nmrXivProvider->url(env('NMRXIV_PROVIDER_URL'));

        $dataCatalog = Schema::DataCatalog();
        $dataCatalog['@id'] = url(env('APP_URL'));
        $dataCatalog['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://schema.org/DataCatalog']);
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

        $Annett = BioschemasHelper::preparePerson('0000-0002-2542-0867', 'Annett', 'Schröter', 'annett.schroeter@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Christian = BioschemasHelper::preparePerson(null, 'Christian', 'Popp', null, null);
        $Christoph = BioschemasHelper::preparePerson('0000-0001-6966-0814', 'Christoph', 'Steinbeck', 'christoph.steinbeck@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Darina = BioschemasHelper::preparePerson(null, 'Darina', 'Storozhuk', 'darina.storozhuk@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $David = BioschemasHelper::preparePerson('0000-0001-7499-1693', 'David', 'Rauh', null, null);
        $Guido = BioschemasHelper::preparePerson('0000-0003-1022-4326', 'Guido', 'Pauli', null, 'University of Illinois');
        $Hamed = BioschemasHelper::preparePerson(null, 'Hamed', 'Musallam', 'hamed.musallam@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Johannes = BioschemasHelper::preparePerson('0000-0003-2060-842X', 'Johannes', 'Liermann', null, null);
        $Julien = BioschemasHelper::preparePerson('0000-0002-3416-2572', 'Julien', 'Wist', null, null);
        $Kohulan = BioschemasHelper::preparePerson('0000-0003-1066-7792', 'Kohulan', 'Rajan', null, null);
        $Luc = BioschemasHelper::preparePerson('0000-0002-4943-2643', 'Luc', 'Patiny', null, null);
        $Markus = BioschemasHelper::preparePerson(null, 'Markus', 'Lange', null, null);
        $Nazar = BioschemasHelper::preparePerson('0000-0002-5870-8496', 'Nazar', 'Stefaniuk', null, null);
        $Nils = BioschemasHelper::preparePerson('0000-0002-0990-9582', 'Nils', 'Schlörer', null, null);
        $Nisha = BioschemasHelper::preparePerson('0009-0006-4755-1039', 'Nisha', 'Sharma', null, null);
        $Noura = BioschemasHelper::preparePerson('0009-0001-5998-5030', 'Noura', 'Rayya', null, null);
        $Pascal = BioschemasHelper::preparePerson(null, 'Pascal', 'Scherreiks', null, null);
        $Stefan = BioschemasHelper::preparePerson('0000-0002-5990-4157', 'Stefan', 'Kuhn', null, null);
        $Steffen = BioschemasHelper::preparePerson('0000-0002-7899-7192', 'Steffen', 'Neumann', null, null);
        $Tillmann = BioschemasHelper::preparePerson('0000-0003-4480-8661', 'Tillmann', 'Fischer', null, null);
        $Venkata = BioschemasHelper::preparePerson('0000-0002-2564-3243', 'Venkata Chandrasekhar', 'Nainala', null, null);

        $contributors = [$Annett, $Christian, $Christoph, $Darina, $Guido, $Hamed, $Johannes, $Julien, $Kohulan, $Luc, $Markus, $Nazar, $Nils, $Nisha, $Noura, $Pascal, $Stefan, $Steffen, $Tillmann, $Venkata];

        return $contributors;
    }
}
