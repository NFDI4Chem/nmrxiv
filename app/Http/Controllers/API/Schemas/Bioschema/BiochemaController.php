<?php

namespace App\Http\Controllers\API\Schemas\Bioschema;

use App\Http\Controllers\Controller;
use App\Models\Bioschema\BioSchema;
use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\SchemaOrg\Schema;

/**
 * Implement Bioschemas profiles types on nmrXiv project, study, and dataset
 * to enable exporting their metadata with a json endpoint, including the
 * samples and molecules.
 */
class BiochemaController extends Controller
{
    /**
     * Implement Bioschemas upon request by model's name to generate a project, study, or dataset schema.
     *
     * @link https://bioschemas.org/profiles/Study/0.3-DRAFT
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     *
     * @param  Illuminate\Http\Request  $request
     * @param  App\Models\User  $username
     * @param  App\Models\Project  $projectName
     * @param  App\Models\Study  $studyName Optional
     * @param  App\Models\Dataset  $datasetName Optional
     * @return object
     */
    public function modelSchemaByName(Request $request, $username, $projectName, $studyName = null, $datasetName = null)
    {
        $user = User::where('username', $username)->firstOrFail();
        if ($user) {
            $project = Project::where([['slug', $projectName], ['owner_id', $user->id]])->firstOrFail();
        }
        if ($project) {
            if ($project->is_public) {
                $projectSchema = $this->project($project);
                if ($studyName) {
                    // send study back with project info added to it\
                    $study = Study::where([['slug', $studyName], ['owner_id', $user->id], ['project_id', $project->id]])->firstOrFail();
                    if ($study) {
                        if ($study->is_public) {
                            $studySchema = $this->study($study);
                            if ($datasetName) {
                                $dataset = Dataset::where([['slug', $datasetName], ['owner_id', $user->id], ['project_id', $project->id], ['study_id', $study->id]])->firstOrFail();
                                // send dataset with project and study details
                                if ($dataset) {
                                    if ($dataset->is_public) {
                                        $datasetSchema = $this->dataset($dataset);
                                    } else {
                                        throw new AuthorizationException;
                                    }

                                    return $datasetSchema;
                                }
                            }

                            return $studySchema;
                        } else {
                            throw new AuthorizationException;
                        }
                    }
                }

                return $projectSchema;
            } else {
                throw new AuthorizationException;
            }
        }
    }

    /**
     * Implement Bioschemas upon request by model id to generate a project, study, or dataset schema.
     *
     * @link https://bioschemas.org/profiles/Study/0.3-DRAFT
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     *
     * @param  Illuminate\Http\Request  $request
     * @param  string  $identifier
     * @return object
     */
    public function modelSchemaByID(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $namespace = $resolvedModel['namespace'];
        $model = $resolvedModel['model'];

        if ($model->is_public) {
            if ($namespace == 'Project') {
                $projectSchema = $this->project($model);

                return $projectSchema;
            } elseif ($namespace == 'Study') {
                $studySchema = $this->study($model);

                return $studySchema;
            } elseif ($namespace == 'Dataset') {

                $datasetSchema = $this->dataset($model);

                return $datasetSchema;
            }
        } else {
            throw new AuthorizationException;
        }
    }

    /**
     * Get Property value from ontologies.
     *
     * @param  string  $ontologyID
     * @param  string  $value
     * @param  string  $unitUrl
     * @return object $propertyValue
     */
    public function getPropertyValue($name, $ontologyID, $value, $unitUrl)
    {
        $propertyValue = Schema::PropertyValue();
        $propertyValue->name($name);
        $propertyValue->propertyID($ontologyID);
        $propertyValue->value($value);
        $propertyValue->unitCode($unitUrl);

        return $propertyValue;
    }

    /**
     * Create a list of all the schemas an object conforms to based on their urls.
     *
     * @link https://www.dublincore.org/specifications/dublin-core/dcmi-terms/#conformsTo
     *
     * @param  array  $urls
     * @return array $confromsToList
     */
    public function conformsTo($urls)
    {
        $confromsToList = [];
        foreach ($urls as &$url) {
            $creativeWork = Schema::CreativeWork();
            $creativeWork['@id'] = $url;
            $confromsTo = $creativeWork;
            array_push($confromsToList, $confromsTo);
        }

        return $confromsToList;
    }

    /**
     * Get the tags (keywords) of a model.
     *
     * @param  object  $model
     * @return object $tags
     */
    public function getTags($model)
    {
        $tags = [];
        foreach ($model->tags as &$tag) {
            $tag = $tag->name;
            array_push($tags, $tag);
        }

        return $tags;
    }

    /**
     * Implement Schemas' Person on a model's authors.
     *
     * @link https://schema.org/Person.
     *
     * @param  object  $model
     * @return array $authors
     */
    public function getAuthors($model)
    {
        $authors = [];
        foreach ($model->authors as &$author) {
            $authorSchema = Schema::Person();
            $authorSchema->givenName($author->given_name);
            $authorSchema->familyName($author->family_name);
            $authorSchema->identifier($author->orcid_id);
            $authorSchema->email($author->email_id);
            $authorSchema->affiliation($author->affiliation);
            array_push($authors, $authorSchema);
        }

        return $authors;
    }

    /**
     * Implement Schema' CreativeWork on a model's citations.
     *
     * @link https://schema.org/CreativeWork
     *
     * @param  object  $model
     * @return array $citations
     */
    public function getCitations($model)
    {
        $citations = [];
        foreach ($model->citations as &$citation) {
            $citationSchema = Schema::CreativeWork();
            $citationSchema->abstract($citation->abstract);
            $citationSchema->author($citation->authors);
            $citationSchema->headline($citation->title);
            $citationSchema->identifier($citation->doi);
            array_push($citations, $citationSchema);
        }

        return $citations;
    }

    /**
     * Implement Bioschemas' MolecularEntity on molecules found in a sample.
     *
     * @link https://bioschemas.org/profiles/MolecularEntity/0.5-RELEASE
     *
     * @param  App\Models\BioSample  $sample
     * @return array $molecules
     */
    public function getMolecules($sample)
    {
        $molecules = [];
        foreach ($sample->molecules as &$molecule) {
            $inchiKey = $molecule->INCHI_KEY;
            $pubchemLink = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/inchikey/'.$inchiKey.'/property/IUPACName/JSON';
            $json = json_decode(Http::get($pubchemLink)->body(), true);
            // $cid = $json['PropertyTable']['Properties'][0]['CID'];
            // $iupacName = $json['PropertyTable']['Properties'][0]['IUPACName'];

            // $moleculeSchema = Schema::MolecularEntity();
            // $moleculeSchema['@id'] = $inchiKey;
            // $moleculeSchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/profiles/MolecularEntity/0.5-RELEASE']);
            // $moleculeSchema['identifier'] = $inchiKey;
            // $moleculeSchema->name($molecule->CAS_NUMBER);
            // $moleculeSchema->url('https://pubchem.ncbi.nlm.nih.gov/compound/'.$cid);
            // $moleculeSchema->inChI('InChI='.$molecule->STANDARD_INCHI);
            // $moleculeSchema->inChIKey($inchiKey);
            // $moleculeSchema->iupacName($iupacName);
            // $moleculeSchema->molecularFormula($molecule->FORMULA);
            // $moleculeSchema->molecularWeight($molecule->MOLECULAR_WEIGHT);
            // $moleculeSchema->smiles([$molecule->SMILES, $molecule->SMILES_CHIRAL, $molecule->CANONICAL_SMILES]);
            // $moleculeSchema->hasRepresentation($molecule->MOL);
            // $moleculeSchema->description('Percentage composition: '.$molecule->pivot->percentage_composition.'%');
            // array_push($molecules, $moleculeSchema);
        }

        return $molecules;
    }

    /**
     * Implement Bioschemas' ChemicalSubstance on samples found in studies.
     *
     * @link https://bioschemas.org/profiles/ChemicalSubstance/0.4-RELEASE
     * @link https://bioschemas.org/profiles/Study/0.3-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $sampleSchema
     */
    public function getSample($study)
    {
        $sample = $study->sample;
        $sampleSchema = BioSchema::ChemicalSubstance();
        $sampleSchema['@id'] = $study->doi;
        $sampleSchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/profiles/ChemicalSubstance/0.4-RELEASE']);
        $sampleSchema->name($study->project->name.'.'.$sample->name);
        $sampleSchema->description($sample->description);
        $sampleSchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $sampleSchema->hasBioChemEntityPart($this->getMolecules($sample));

        return $sampleSchema;
    }

    /**
     * Get NMRium info from a dataset.
     *
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Study/0.3-DRAFT
     *
     * @param  App\Models\Dataset  $dataset
     * @return array $nmriumInfo
     */
    public function getNMRiumInfo($dataset)
    {
        $nmrium = $dataset->nmrium;
        if (! $nmrium) {
            $study = $dataset->study;
            $NMRiumInfo = json_decode($study->nmrium->nmrium_info);
            foreach ($NMRiumInfo->data->spectra as $spectra) {
                $fileSource = $spectra->sourceSelector->files[0];
                $fileName = pathinfo($fileSource);
                if ($fileName['basename'] == $dataset->fsObject->name) {
                    $info = $spectra->info;
                }
            }
        } else {
            $NMRiumInfo = json_decode($nmrium->nmrium_info);
            $spectra = $NMRiumInfo->data->spectra[0];
            $info = $spectra->info;
        }
        $solvent = $info->solvent;
        $nucleus = $info->nucleus;
        if (is_string($nucleus)) {
            $nucleus = [$nucleus];
        }
        $dimension = $info->dimension;
        $probeName = $info->probeName;
        $experiment = $info->experiment;
        $temperature = $info->temperature;
        $baseFrequency = $info->baseFrequency;
        $fieldStrength = $info->fieldStrength;
        $numberOfScans = $info->numberOfScans;
        $pulseSequence = $info->pulseSequence;
        $spectralWidth = $info->spectralWidth;
        $numberOfPoints = $info->numberOfPoints;
        $relaxationTime = $info->relaxationTime;

        $solventProperty = $this->getPropertyValue('NMR solvent', 'NMR:1000330', $solvent, null);
        $nucleusProperty = $this->getPropertyValue('acquisition nucleus', 'NMR:1400083', $nucleus, null);
        $dimensionProperty = $this->getPropertyValue('NMR spectrum by dimensionality', 'NMR:1000117', $dimension, null);
        $probeNameProperty = $this->getPropertyValue('NMR probe', 'OBI:0000516', $probeName, null);
        $experimentProperty = $this->getPropertyValue('pulsed nuclear magnetic resonance spectroscopy', 'CHMO:0000613', $experiment, null);
        $temperatureProperty = $this->getPropertyValue('Temperature', 'NCIT:C25206', $temperature, 'https://ontobee.org/ontology/UO?iri=http://purl.obolibrary.org/obo/UO_0000012');
        $baseFrequencyProperty = $this->getPropertyValue('irradiation frequency', 'NMR:1400026', $baseFrequency, 'http://purl.obolibrary.org/obo/UO_0000325');
        $fieldStrengthProperty = $this->getPropertyValue('magnetic field strength', 'MR:1400253', $fieldStrength, 'http://purl.obolibrary.org/obo/UO_0000228');
        $numberOfScansProperty = $this->getPropertyValue('number of scans', 'NMR:1400087', $numberOfScans, 'scans');
        $pulseSequenceProperty = $this->getPropertyValue('nuclear magnetic resonance pulse sequence', 'CHMO:0001841', $pulseSequence, null);
        $spectralWidthProperty = $this->getPropertyValue('Spectral Width', 'NCIT:C156496', $spectralWidth, null); //todo: add unit
        $numberOfPointsProperty = $this->getPropertyValue('number of data points', 'NMR:1000176', $numberOfPoints, 'points');
        $relaxationTimeProperty = $this->getPropertyValue('relaxation time measurement', 'FIX:0000202', $relaxationTime, 'http://purl.obolibrary.org/obo/UO_0000010');

        $keywords = [$solvent, $dimension.'D', $experiment];
        foreach ($nucleus as $e) {
            array_push($keywords, $e);
        }
        $variables = [$solventProperty, $nucleusProperty,  $dimensionProperty, $probeNameProperty,
            $experimentProperty, $temperatureProperty, $baseFrequencyProperty, $fieldStrengthProperty, $numberOfScansProperty, $pulseSequenceProperty, $spectralWidthProperty, $numberOfPointsProperty, $relaxationTimeProperty, ];

        return [$keywords, $variables, $experiment];
    }

    /**
     * Get Dataset download details info from a dataset.
     *
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://schema.org/distribution
     * @link https://schema.org/DataDownload
     *
     * @param  App\Models\Dataset  $dataset
     * return object $distribution
     */
    public function getDistribution($dataset)
    {
        $url = env('APP_URL');
        $user = $dataset->owner;
        $slug = $dataset->project->slug;
        $contentURL = $url.'/'.$user.'/datasets/'.$slug;

        $distribution = Schema::DataDownload();
        $distribution->name($dataset->project->name);
        $distribution->encodingFormat('zip');
        $distribution->contentURL($contentURL);
    }

    /**
     * Implement Bioschemas' DataCatalog with only few properties to be
     * included in the dataset schema.
     *
     * @link https://bioschemas.org/profiles/DataCatalog/0.3-RELEASE-2019_07_01
     *
     * @return object $dataCatalogSchema
     */
    public function DataCatalogLite()
    {
        $dataCatalogSchema = Schema::DataCatalog();
        $dataCatalogSchema->name(env('APP_NAME'));
        $dataCatalogSchema->url(env('APP_URL'));

        return $dataCatalogSchema;
    }

    /**
     * Implement Bioschemas' study with only few properties, including the sample and molecules,
     * to be included in the project schema.
     *
     * @link https://bioschemas.org/profiles/Study/0.3-DRAFT
     *
     * @param  App\Models\Project  $project
     * @return object $projectSchema
     */
    public function prepareStudies($project)
    {
        $schemas = [];
        foreach ($project->studies as $study) {
            $studySchema = $this->studyLite($study);
            $studySchema->hasPart($this->prepareDatasets($study));
            array_push($schemas, $studySchema);
        }

        return $schemas;
    }

    public function prepareDatasets($study)
    {
        $schemas = [];
        foreach ($study->datasets as $dataset) {
            $datasetSchema = $this->datasetLite($dataset);
            array_push($schemas, $datasetSchema);
        }

        return $schemas;
    }

    public function preparePublisher()
    {
        $publisher = Schema::Organization();
        $publisher->name(env('APP_NAME'));
        $publisher->url(env('APP_URL'));

        return $publisher;
    }

    /**
     * Implement Bioschemas' Dataset, along with the project and study it belongs to.
     *
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Study/0.3-DRAFT
     *
     * @param  App\Models\Dataset  $dataset
     * @param  App\Models\Study  $study
     * @param  App\Models\Project  $project
     * @return object $datasetSchema
     */
    public function datasetLite($dataset)
    {
        $prefix = $dataset->study->project->name.':'.$dataset->study->name.'.';
        $datasetSchema = Schema::Dataset();
        $datasetSchema['@id'] = $dataset->doi;
        $datasetSchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/profiles/Dataset/1.0-RELEASE', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#assay']);
        $datasetSchema->name($prefix.$this->getNMRiumInfo($dataset)[2]);
        $datasetSchema->description($dataset->description);
        $datasetSchema->keywords($this->getNMRiumInfo($dataset)[0]);
        $datasetSchema->license($dataset->study->license->url);
        $datasetSchema->url(env('APP_URL').'/'.explode(':', $dataset->identifier ? $dataset->identifier : ':')[1]);
        $datasetSchema->dateCreated($dataset->created_at);
        $datasetSchema->dateModified($dataset->updated_at);
        $datasetSchema->datePublished($dataset->release_date);
        $datasetSchema->distribution($this->getDistribution($dataset));
        $datasetSchema->includedInDataCatalog($this->DataCatalogLite());
        $datasetSchema->measurementTechnique(env('MEASUREMENT_TECHNIQUE'));
        $datasetSchema->variableMeasured($this->getNMRiumInfo($dataset)[1]);
        $datasetSchema->isAccessibleForFree(true);

        return $datasetSchema;
    }

    public function dataset($dataset)
    {
        $datasetSchema = $this->datasetLite($dataset);
        $projectSchema = $this->projectLite($dataset->project);
        $studySchema = $this->studyLite($dataset->study);
        $studySchema->isPartOf($projectSchema);
        $datasetSchema->isPartOf($studySchema);

        return $datasetSchema;
    }

    /**
     * Implement Bioschemas' Study, including the sample and molecules, along
     * with the project it belongs to and, briefly, the datasets it contains.
     *
     * @link https://bioschemas.org/profiles/Study/0.3-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $studySchema
     */
    public function studyLite($study)
    {
        $prefix = $study->project->name.':';
        $studySchema = BioSchema::Study();
        $studySchema['@id'] = $study->doi;
        $studySchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/profiles/Study/0.3-DRAFT', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#study']);
        $studySchema->name($prefix.$study->name);
        $studySchema->description($study->description);
        $studySchema->keywords($this->getTags($study));
        $studySchema->license($study->license->url);
        $studySchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $studySchema->dateCreated($study->created_at);
        $studySchema->dateModified($study->updated_at);
        $studySchema->datePublished($study->release_date);
        $studySchema->about($this->getSample($study));
        $studySchema->studyDomain('Chemistry');
        $studySchema->studySubject('Small molecules');

        return $studySchema;
    }

    public function study($study)
    {
        $studySchema = $this->studyLite($study);
        $studySchema->isPartOf($this->projectLite($study->project));
        $studySchema->hasPart($this->prepareDatasets($study));

        return $studySchema;
    }

    /**
     * Implement Bioschemas' project along with brief details about
     * the studies and datasets it contains.
     *
     * @link https://bioschemas.org/profiles/Study/0.3-DRAFT
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10
     * @link https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02
     *
     * @param  App\Models\Project  $project
     * @return object $projectSchema
     */
    public function projectLite($project)
    {
        $projectSchema = BioSchema::Study();
        $projectSchema['@id'] = $project->doi;
        $projectSchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/profiles/Study/0.3-DRAFT', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#investigation']);
        $projectSchema->name($project->name);
        $projectSchema->description($project->description);
        $projectSchema->keywords($this->getTags($project));
        $projectSchema->license($project->license->url);
        $projectSchema->publisher($this->preparePublisher());
        $projectSchema->url(env('APP_URL').'/'.explode(':', $project->identifier ? $project->identifier : ':')[1]);
        $projectSchema->dateCreated($project->created_at);
        $projectSchema->dateModified($project->updated_at);
        $projectSchema->datePublished($project->release_date);
        $projectSchema->author($this->getAuthors($project));
        $projectSchema->citation($this->getCitations($project));

        return $projectSchema;
    }

    public function project($project)
    {
        $projectSchema = $this->projectLite($project);
        $projectSchema->hasPart($this->prepareStudies($project));

        return $projectSchema;
    }
}
