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
     * @return object $dataCatalogSchema
     */
    public function dataCatalogSchema(Request $request)
    {
        $keywords = $this->prepareKeywords();
        $contributors = $this->prepareContributors();

        $nmrXivProvider = Schema::Organization();
        $nmrXivProvider->name(env('NMRXIV_PROVIDER'));
        $nmrXivProvider->url(env('NMRXIV_PROVIDER_URL'));

        $dataCatalogSchema = Schema::DataCatalog();
        $dataCatalogSchema['@id'] = url(env('APP_URL'));
        $dataCatalogSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://schema.org/DataCatalog']);
        $dataCatalogSchema->description(env('APP_DESCRIPTION'));
        $dataCatalogSchema->keywords($keywords);
        $dataCatalogSchema->name(env('APP_NAME'));
        $dataCatalogSchema->provider($nmrXivProvider);
        $dataCatalogSchema->url(env('APP_URL'));
        $dataCatalogSchema->identifier(env('APP_URL'));
        $dataCatalogSchema->license('https://mit-license.org/');
        $dataCatalogSchema->contributor($contributors);
        $dataCatalogSchema->isAccessibleForFree(true);

        $dataCatalogSchema->measurementTechnique(env('MEASUREMENT_TECHNIQUE'));

        return $dataCatalogSchema;
    }

    /**
     * Prepare keywords associated with nmrXiv.
     *
     * @return array $keywords
     */
    public function prepareKeywords()
    {
        //Prepare Defined Term Sets
        $chmo = BioschemasHelper::prepareDefinedTermSet('Chemical Methods Ontology', 'http://purl.obolibrary.org/obo/chmo.owl');
        $nmrcv = BioschemasHelper::prepareDefinedTermSet('nuclear magnetic resonance CV', 'http://nmrml.org/cv/');

        //Prepare Defined Terms
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
     * Prepare contributors to nmrXiv.
     *
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
        $Johannes = BioschemasHelper::preparePerson('0000-0003-2060-842X', 'Johannes', 'Liermann', 'liermann@uni-mainz.de', 'Johannes Gutenberg-Universität Mainz');
        $Julien = BioschemasHelper::preparePerson('0000-0002-3416-2572', 'Julien', 'Wist', null, 'Murdoch University: Murdoch, WA, AU');
        $Kohulan = BioschemasHelper::preparePerson('0000-0003-1066-7792', 'Kohulan', 'Rajan', 'kohulan.rajan@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Luc = BioschemasHelper::preparePerson('0000-0002-4943-2643', 'Luc', 'Patiny', null, 'École Polytechnique Fédérale de Lausanne: Lausanne, VD, CH');
        $Markus = BioschemasHelper::preparePerson(null, 'Markus', 'Lange', null, null);
        $Nazar = BioschemasHelper::preparePerson('0000-0002-5870-8496', 'Nazar', 'Stefaniuk', null, null);
        $Nils = BioschemasHelper::preparePerson('0000-0002-0990-9582', 'Nils', 'Schlörer', null, 'Friedrich-Schiller-Universität Jena');
        $Nisha = BioschemasHelper::preparePerson('0009-0006-4755-1039', 'Nisha', 'Sharma', 'nisha.sharma@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Noura = BioschemasHelper::preparePerson('0009-0001-5998-5030', 'Noura', 'Rayya', 'noura.rayya@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Pascal = BioschemasHelper::preparePerson(null, 'Pascal', 'Scherreiks', null, null);
        $Stefan = BioschemasHelper::preparePerson('0000-0002-5990-4157', 'Stefan', 'Kuhn', null, 'University of Tartu, Tartu');
        $Steffen = BioschemasHelper::preparePerson('0000-0002-7899-7192', 'Steffen', 'Neumann', null, 'Leibniz-Institut für Pflanzenbiochemie, Halle');
        $Tillmann = BioschemasHelper::preparePerson('0000-0003-4480-8661', 'Tillmann', 'Fischer', null, 'Leibniz-Institut für Pflanzenbiochemie, Halle');
        $Venkata = BioschemasHelper::preparePerson('0000-0002-2564-3243', 'Venkata Chandrasekhar', 'Nainala', 'chandu.nainala@uni-jena.de', 'Friedrich-Schiller-Universität Jena');

        $contributors = [$Annett, $Christian, $Christoph, $Darina, $Guido, $Hamed, $Johannes, $Julien, $Kohulan, $Luc, $Markus, $Nazar, $Nils, $Nisha, $Noura, $Pascal, $Stefan, $Steffen, $Tillmann, $Venkata];

        return $contributors;
    }
}
