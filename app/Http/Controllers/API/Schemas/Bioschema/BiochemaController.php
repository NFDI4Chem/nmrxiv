<?php

namespace App\Http\Controllers\API\Schemas\Bioschema;

use App\Http\Controllers\Controller;
use App\Models\Bioschema\BioSchema;
use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Http\Request;
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
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
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
            $projectSchema = $this->project($project);
            if ($studyName) {
                // send study back with project info added to it\
                $study = Study::where([['slug', $studyName], ['owner_id', $user->id], ['project_id', $project->id]])->firstOrFail();
                if ($study) {
                    $studySchema = $this->study($study, $project);
                    if ($datasetName) {
                        $dataset = Dataset::where([['slug', $datasetName], ['owner_id', $user->id], ['project_id', $project->id], ['study_id', $study->id]])->firstOrFail();
                        // send dataset with project and study details
                        if ($dataset) {
                            $datasetSchema = $this->dataset($dataset, $study, $project);

                            return $datasetSchema;
                        }
                    }

                    return $studySchema;
                }
            }

            return $projectSchema;
        }
    }

    /**
     * Implement Bioschemas upon request by model id to generate a project, study, or dataset schema.
     *
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
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

        if ($namespace == 'Project') {
            $projectSchema = $this->project($model);

            return $projectSchema;
        } elseif ($namespace == 'Study') {
            $studySchema = $this->study($model);

            return $studySchema;
        } elseif ($namespace == 'Dataset') {
            $project = $model->project;
            $study = $model->study;
            $datasetSchema = $this->dataset($model, $study, $project);

            return $datasetSchema;
        }
    }

    /**
     * Get Property value from ontologies.
     *
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
            $creativeWork = Schema::creativeWork();
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

        return  $authors;
    }

    /**
     * Implement Schema' Article on a model's citations.
     *
     * @link https://schema.org/Article
     *
     * @param  object  $model
     * @return array $citations
     */
    public function getCitations($model)
    {
        $citations = [];
        foreach ($model->citations as &$citation) {
            $citationSchema = Schema::Article();
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
     * @link https://bioschemas.org/types/BioSample/0.1-RELEASE-2019_06_19
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
            $json = file_get_contents($pubchemLink);
            $jsonDecode = json_decode($json, true);
            $cid = $jsonDecode['PropertyTable']['Properties'][0]['CID'];
            $iupacName = $jsonDecode['PropertyTable']['Properties'][0]['IUPACName'];

            $moleculeSchema = Schema::MolecularEntity();
            $moleculeSchema['@id'] = $inchiKey;
            $moleculeSchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/profiles/MolecularEntity/0.5-RELEASE']);
            $moleculeSchema['identifie'] = $inchiKey;
            $moleculeSchema->name($molecule->CAS_NUMBER);
            $moleculeSchema->url('https://pubchem.ncbi.nlm.nih.gov/compound/'.$cid);
            $moleculeSchema->inChI($molecule->STANDARD_INCHI);
            $moleculeSchema->inChIKey($inchiKey);
            $moleculeSchema->iupacName($iupacName);
            $moleculeSchema->molecularFormula($molecule->FORMULA);
            $moleculeSchema->molecularWeight($molecule->MOLECULAR_WEIGHT);
            $moleculeSchema->smiles([$molecule->SMILES, $molecule->SMILES_CHIRAL, $molecule->CANONICAL_SMILES]);
            $moleculeSchema->hasRepresentation($molecule->MOL);
            $moleculeSchema->description('Percentage composition: '.$molecule->pivot->percentage_composition.'%');
            array_push($molecules, $moleculeSchema);
        }

        return $molecules;
    }

    /**
     * Implement Bioschemas' BioSample on samples found in studies.
     *
     * @link https://bioschemas.org/types/BioSample/0.1-RELEASE-2019_06_19
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $sampleSchema
     */
    public function getSample($study)
    {
        $sample = $study->sample;

        $sampleSchema = BioSchema::BioSample();
        $sampleSchema['@id'] = $study->doi;
        $sampleSchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/types/BioSample/0.1-RELEASE-2019_06_19']);
        $sampleSchema['identifie'] = explode(':', $study->identifier ? $study->identifier : ':')[1];
        $sampleSchema->name($sample->name);
        $sampleSchema->description($sample->description);
        $sampleSchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $sampleSchema->hasBioChemEntityPart($this->getMolecules($sample));

        return $sampleSchema;
    }

    /**
     * Get NMRium info from a dataset.
     *
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     *
     * @param  App\Models\Dataset  $dataset
     * @return array $nmriumInfo
     */
    public function getNMRiumInfo($dataset)
    {
        $nmrium = NMRium::where([['dataset_id', $dataset->id]])->firstOrFail();
        $info = json_decode($nmrium->nmrium_info)->spectra[0]->info;

        $nucleus = $info->nucleus;
        $solvent = $info->solvent;
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

        $nucleusProperty = $this->getPropertyValue('acquisition nucleus', 'NMR:1400083', $nucleus, null);
        $solventProperty = $this->getPropertyValue('NMR solvent', 'NMR:1000330', $solvent, null);
        $dimensionProperty = $this->getPropertyValue('NMR spectrum by dimensionality', 'NMR:1000117', $dimension, null);
        $probeNameProperty = $this->getPropertyValue('NMR probe', 'OBI_0000516', $probeName, null);
        $experimentProperty = $this->getPropertyValue('NMR by nucleus type', 'FIX_0000132', $experiment, null);
        $temperatureProperty = $this->getPropertyValue('Temperature', 'NCIT_C25206', $temperature, 'https://ontobee.org/ontology/UO?iri=http://purl.obolibrary.org/obo/UO_0000012');
        $baseFrequencyProperty = $this->getPropertyValue('irradiation frequency', 'NMR:1400026', $baseFrequency, 'http://purl.obolibrary.org/obo/UO_0000325');
        $fieldStrengthProperty = $this->getPropertyValue('magnetic field strength', 'MR:1400253', $fieldStrength, 'http://purl.obolibrary.org/obo/UO_0000228');
        $numberOfScansProperty = $this->getPropertyValue('number of scans', 'NMR:1400087', $numberOfScans, 'scans');
        $pulseSequenceProperty = $this->getPropertyValue('nuclear magnetic resonance pulse sequence', 'CHMO:0001841', $pulseSequence, null);
        $spectralWidthProperty = $this->getPropertyValue('Spectral Width', 'NCIT_C156496', $spectralWidth, null); //todo: add unit
        $numberOfPointsProperty = $this->getPropertyValue('number of data points', 'NMR:1000176', $numberOfPoints, 'points');
        $relaxationTimeProperty = $this->getPropertyValue('relaxation time measurement', 'FIX_0000202', $relaxationTime, 'http://purl.obolibrary.org/obo/UO_0000010');

        $keywords = [$nucleus, $solvent, $dimension.'D', $experiment];
        $variables = [$nucleusProperty, $solventProperty, $dimensionProperty, $probeNameProperty,
            $experimentProperty, $temperatureProperty, $baseFrequencyProperty, $fieldStrengthProperty, $numberOfScansProperty, $pulseSequenceProperty, $spectralWidthProperty, $numberOfPointsProperty, $relaxationTimeProperty, ];

        return [$keywords, $variables];
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
        $dataCatalogSchema = Schema::dataCatalog();
        $dataCatalogSchema->name(env('APP_NAME'));
        $dataCatalogSchema->url(env('APP_URL'));

        return $dataCatalogSchema;
    }

    /**
     * Implement Bioschemas' MolecularEntity with only few properties to be
     * included in the lite studies.
     *
     * @link https://bioschemas.org/profiles/MolecularEntity/0.5-RELEASE
     * @link https://bioschemas.org/types/BioSample/0.1-RELEASE-2019_06_19
     *
     * @param  App\Models\BioSample  $sample
     * @return object $molecules
     */
    public function moleculesLite($sample)
    {
        $molecules = [];
        foreach ($sample->molecules as &$molecule) {
            $moleculeSchema = Schema::MolecularEntity();
            $moleculeSchema->inChI($molecule->STANDARD_INCHI);
            $moleculeSchema->description('Percentage composition: '.$molecule->pivot->percentage_composition.'%');
            array_push($molecules, $moleculeSchema);
        }

        return $molecules;
    }

    /**
     * Implement Bioschemas' BioSample with only few properties to be
     * included in the lite studies.
     *
     * @link https://bioschemas.org/types/BioSample/0.1-RELEASE-2019_06_19
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $sampleSchema
     */
    public function sampleLite($study)
    {
        $sampleSchema = BioSchema::BioSample();
        $sample = $study->sample;
        $molecules = $this->moleculesLite($sample);

        $sampleSchema->name($sample->name);
        $sampleSchema->hasBioChemEntityPart($molecules);

        return $sampleSchema;
    }

    /**
     * Implement Bioschemas' Dataset with only few properties to be included
     * in the project schema or the study schema.
     *
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return array $datasets
     */
    public function datasetsLite($study)
    {
        $datasets = [];
        foreach ($study->datasets as &$dataset) {
            $datasetSchema = Schema::Dataset();
            $datasetSchema->name($dataset->name);
            $datasetSchema->url(env('APP_URL').'/'.explode(':', $dataset->identifier ? $dataset->identifier : ':')[1]);
            array_push($datasets, $datasetSchema);
        }

        return $datasets;
    }

    /**
     * Implement Bioschemas' study with only few properties, including the sample and molecules,
     * to be included in the project schema.
     *
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     *
     * @param  App\Models\Project  $project
     * @return array $studies
     */
    public function studyLite($project)
    {
        $studies = [];
        foreach ($project->studies as &$study) {
            $studySchema = BioSchema::Study();
            $studySchema->name($study->name);
            $studySchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
            $studySchema->about($this->sampleLite($study));
            $studySchema->hasPart($this->datasetsLite($study));
            array_push($studies, $studySchema);
        }

        return $studies;
    }

    /**
     * Implement Bioschemas' Dataset, along with the project and study it belongs to.
     *
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     *
     * @param  App\Models\Dataset  $dataset
     * @param  App\Models\Study  $study
     * @param  App\Models\Project  $project
     * @return object $datasetSchema
     */
    public function dataset($dataset, $study, $project)
    {
        $datasetSchema = Schema::Dataset();
        $datasetSchema['@id'] = $dataset->doi;
        $datasetSchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/profiles/Dataset/1.0-RELEASE', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#assay']);
        $datasetSchema['identifier'] = explode(':', $dataset->identifier ? $dataset->identifier : ':')[1];
        $datasetSchema->name($dataset->name);
        $datasetSchema->description($dataset->description);
        $datasetSchema->keywords($this->getNMRiumInfo($dataset)[0]);
        $datasetSchema->license($study->license->url);
        $datasetSchema->url(env('APP_URL').'/'.explode(':', $dataset->identifier ? $dataset->identifier : ':')[1]);
        $datasetSchema->dateCreated($dataset->created_at);
        $datasetSchema->dateModified($dataset->updated_at);
        $datasetSchema->datePublished($dataset->release_date);
        $datasetSchema->includedInDataCatalog($this->DataCatalogLite());
        $datasetSchema->measurementTechnique(env('MEASUREMENT_TECHNIQUE'));
        $datasetSchema->variableMeasured($this->getNMRiumInfo($dataset)[1]);
        $datasetSchema->isAccessibleForFree(true);
        $projectSchema = $this->project($project);
        $studySchema = $this->study($study, $project);
        $datasetSchema->isPartOf([$projectSchema, $studySchema]);

        return $datasetSchema;
    }

    /**
     * Implement Bioschemas' Study, including the sample and molecules, along
     * with the project it belongs to and, briefly, the datasets it contains.
     *
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $studySchema
     */
    public function study($study, $project)
    {
        $studySchema = BioSchema::Study();
        $studySchema['@id'] = $study->doi;
        $studySchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/profiles/Study/0.2-DRAFT', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#study']);
        $studySchema['identifier'] = explode(':', $study->identifier ? $study->identifier : ':')[1];
        $studySchema->name($study->name);
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
        $studySchema->isPartOf($this->project($project));
        $studySchema->hasPart($this->datasetsLite($study));

        return $studySchema;
    }

    /**
     * Implement Bioschemas' project along with brief details about
     * the studies and datasets it contains.
     *
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10
     * @link https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02
     *
     * @param  App\Models\Project  $project
     * @return object $projectSchema
     */
    public function project($project)
    {
        $projectSchema = BioSchema::Study();
        $projectSchema['@id'] = $project->doi;
        $projectSchema['dct:conformsTo'] = $this->conformsTo(['https://bioschemas.org/profiles/Study/0.2-DRAFT', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#investigation']);
        $projectSchema['identifier'] = explode(':', $project->identifier ? $project->identifier : ':')[1];
        $projectSchema->name($project->name);
        $projectSchema->description($project->description);
        $projectSchema->keywords($this->getTags($project));
        $projectSchema->license($project->license->url);
        $projectSchema->url(env('APP_URL').'/'.explode(':', $project->identifier ? $project->identifier : ':')[1]);
        $projectSchema->dateCreated($project->created_at);
        $projectSchema->dateModified($project->updated_at);
        $projectSchema->datePublished($project->release_date);
        $projectSchema->author($this->getAuthors($project));
        $projectSchema->citation($this->getCitations($project));

        $projectSchema->hasPart($this->studyLite($project));

        return $projectSchema;
    }
}
