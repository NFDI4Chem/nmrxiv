<?php

namespace App\Http\Controllers\API\Schemas\Bioschemas;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Schemas\Bioschemas;
use App\Models\Study;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\SchemaOrg\Schema;

/**
 * Implement Bioschemas types on nmrXiv project, study, and dataset, including the
 * samples and molecules details.
 */
class BioschemasController extends Controller
{
    /**
     * Implement Bioschemas upon request by model's name to generate a project, study, or dataset schema.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     * @link https://schema.org/Dataset
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
                    $study = Study::where([['slug', $studyName], ['owner_id', $user->id], ['project_id', $project->id]])->firstOrFail();
                    if ($study) {
                        if ($study->is_public) {
                            $studySchema = $this->study($study);
                            if ($datasetName) {
                                $dataset = Dataset::where([['slug', $datasetName], ['owner_id', $user->id], ['project_id', $project->id], ['study_id', $study->id]])->firstOrFail();
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
     * Implement Bioschemas upon request by model's id to generate a project, study, or dataset schema.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     * @link https://schema.org/Dataset
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
     * Use Bioschemas MolecularEntity type to represent molecules found in a sample.
     *
     * @link https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02
     *
     * @param  App\Models\Sample  $sample
     * @return array $moleculesSchemas
     */
    public function prepareMoleculesSchemas($sample)
    {
        $moleculesSchemas = [];

        foreach ($sample->molecules as &$molecule) {
            $inchiKey = $molecule->inchi_key;
            $pubchemLink = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/inchikey/'.$inchiKey.'/property/IUPACName/JSON';
            $json = json_decode(Http::get($pubchemLink)->body(), true);
            $cid = $json['PropertyTable']['Properties'][0]['CID'];
            $iupacName = $json['PropertyTable']['Properties'][0]['IUPACName'];

            $moleculeSchema = Schema::MolecularEntity();
            $moleculeSchema['@id'] = $inchiKey;
            $moleculeSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02']);
            $moleculeSchema['identifier'] = $inchiKey;
            $moleculeSchema->name($molecule->cas);
            $moleculeSchema->url('https://pubchem.ncbi.nlm.nih.gov/compound/'.$cid);
            $moleculeSchema->inChI($molecule->standard_inchi);
            $moleculeSchema->inChIKey($inchiKey);
            $moleculeSchema->iupacName($iupacName);
            $moleculeSchema->molecularFormula($molecule->molecular_formula);
            $moleculeSchema->molecularWeight($molecule->molecular_weight);
            $moleculeSchema->smiles([$molecule->SMILES, $molecule->absolute_smiles, $molecule->canonical_smiles]);
            $moleculeSchema->hasRepresentation($molecule->MOL);
            $moleculeSchema->description('Percentage composition: '.$molecule->pivot->percentage_composition.'%');
            array_push($moleculesSchemas, $moleculeSchema);
        }

        return $moleculesSchemas;
    }

    /**
     * Use Bioschemas ChemicalSubstance type to represent samples found in studies.
     *
     * @link https://bioschemas.org/types/ChemicalSubstance/0.3-RELEASE-2019_09_02
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $sampleSchema
     */
    public function getSample($study)
    {
        $sample = $study->sample;
        $molecules = $this->prepareMoleculesSchemas($sample);

        $sampleSchema = Schema::ChemicalSubstance();
        $sampleSchema['@id'] = $study->doi;
        $sampleSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://bioschemas.org/types/ChemicalSubstance/0.3-RELEASE-2019_09_02']);
        $sampleSchema->name($study->project->name.'.'.$sample->name);
        $sampleSchema->description($sample->description);
        $sampleSchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $sampleSchema->hasBioChemEntityPart($this->prepareMoleculesSchemas($sample));

        return $sampleSchema;
    }

    /**
     * Represent NMRium info as PropertyValue schemas.
     *
     * @param  App\Models\Dataset  $dataset
     * @return array $array
     */
    public function prepareNMRiumInfo($dataset)
    {
        $info = BioschemasHelper::getNMRiumInfo($dataset);
        if ($info) {
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

            $solventProperty = BioschemasHelper::preparePropertyValue('NMR solvent', 'NMR:1000330', $solvent, null);
            $nucleusProperty = BioschemasHelper::preparePropertyValue('acquisition nucleus', 'NMR:1400083', $nucleus, null);
            $dimensionProperty = BioschemasHelper::preparePropertyValue('NMR spectrum by dimensionality', 'NMR:1000117', $dimension, null);
            $probeNameProperty = BioschemasHelper::preparePropertyValue('NMR probe', 'OBI:0000516', $probeName, null);
            $experimentProperty = BioschemasHelper::preparePropertyValue('pulsed nuclear magnetic resonance spectroscopy', 'CHMO:0000613', $experiment, null);
            $temperatureProperty = BioschemasHelper::preparePropertyValue('Temperature', 'NCIT:C25206', $temperature, 'https://ontobee.org/ontology/UO?iri=http://purl.obolibrary.org/obo/UO_0000012');
            $baseFrequencyProperty = BioschemasHelper::preparePropertyValue('irradiation frequency', 'NMR:1400026', $baseFrequency, 'http://purl.obolibrary.org/obo/UO_0000325');
            $fieldStrengthProperty = BioschemasHelper::preparePropertyValue('magnetic field strength', 'MR:1400253', $fieldStrength, 'http://purl.obolibrary.org/obo/UO_0000228');
            $numberOfScansProperty = BioschemasHelper::preparePropertyValue('number of scans', 'NMR:1400087', $numberOfScans, 'scans');
            $pulseSequenceProperty = BioschemasHelper::preparePropertyValue('nuclear magnetic resonance pulse sequence', 'CHMO:0001841', $pulseSequence, null);
            $spectralWidthProperty = BioschemasHelper::preparePropertyValue('Spectral Width', 'NCIT:C156496', $spectralWidth, null); //todo: add unit
            $numberOfPointsProperty = BioschemasHelper::preparePropertyValue('number of data points', 'NMR:1000176', $numberOfPoints, 'points');
            $relaxationTimeProperty = BioschemasHelper::preparePropertyValue('relaxation time measurement', 'FIX:0000202', $relaxationTime, 'http://purl.obolibrary.org/obo/UO_0000010');

            $keywords = [$solvent, $dimension.'D', $experiment];
            foreach ($nucleus as $keyword) {
                array_push($keywords, $keyword);
            }
            $variables = [$solventProperty, $nucleusProperty,  $dimensionProperty, $probeNameProperty,
                $experimentProperty, $temperatureProperty, $baseFrequencyProperty, $fieldStrengthProperty, $numberOfScansProperty, $pulseSequenceProperty, $spectralWidthProperty, $numberOfPointsProperty, $relaxationTimeProperty, ];

            $array = [$keywords, $variables, $experiment];

            return $array;
        }
    }

    /**
     *  Use Bioschemas Study type to represent studies found in a project with their datasets.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Project  $project
     * @return array $studiesSchemas
     */
    public function prepareStudies($project)
    {
        $studiesSchemas = [];
        foreach ($project->studies as $study) {
            $studySchema = $this->studyLite($study);
            $studySchema->hasPart($this->prepareDatasets($study));
            array_push($studiesSchemas, $studySchema);
        }

        return $studiesSchemas;
    }

    /**
     * Use Schema.org Dataset type to represent datasets found in a study.
     *
     * @link https://schema.org/Dataset
     *
     * @param  App\Models\Study  $study
     * @return array $datasetsSchemas
     */
    public function prepareDatasets($study)
    {
        $datasetsSchemas = [];
        foreach ($study->datasets as $dataset) {
            $datasetSchema = $this->datasetLite($dataset);
            array_push($datasetsSchemas, $datasetSchema);
        }

        return $datasetsSchemas;
    }

    /**
     * Use Schema.org Dataset type to represent an nmrXiv dataset without its relations.
     *
     * @link https://schema.org/Dataset
     *
     * @param  App\Models\Dataset  $dataset
     * @return object $datasetSchema
     */
    public function datasetLite($dataset)
    {
        $nmriumInfo = $this->prepareNMRiumInfo($dataset);
        if ($nmriumInfo) {
            $prefix = $dataset->study->project->name.':'.$dataset->study->name.'.';
            $datasetSchema = Schema::Dataset();
            $datasetSchema['@id'] = $dataset->doi;
            $datasetSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://schema.org/Dataset', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#assay']);
            $datasetSchema->name($prefix.$nmriumInfo[2]);
            $datasetSchema->description($dataset->description);
            $datasetSchema->keywords($nmriumInfo[0]);
            $datasetSchema->license($dataset->study->license->url);
            $datasetSchema->url(env('APP_URL').'/'.explode(':', $dataset->identifier ? $dataset->identifier : ':')[1]);
            $datasetSchema->dateCreated($dataset->created_at);
            $datasetSchema->dateModified($dataset->updated_at);
            $datasetSchema->datePublished($dataset->release_date);
            $datasetSchema->distribution(BioschemasHelper::prepareDataDownload($dataset));
            $datasetSchema->includedInDataCatalog(BioschemasHelper::prepareDataCatalogLite());
            $datasetSchema->measurementTechnique(env('MEASUREMENT_TECHNIQUE'));
            $datasetSchema->variableMeasured($nmriumInfo[1]);
            $datasetSchema->isAccessibleForFree(true);

            return $datasetSchema;
        }
    }

    /**
     * Use Schema.org Dataset type to represent an nmrXiv dataset with its relations.
     *
     * @link https://schema.org/Dataset
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Dataset  $dataset
     * @return object $datasetSchema
     */
    public function dataset($dataset)
    {
        $projectSchema = $this->projectLite($dataset->project);
        $studySchema = $this->studyLite($dataset->study);
        $datasetSchema = $this->datasetLite($dataset);

        $studySchema->isPartOf($projectSchema);
        $datasetSchema->isPartOf($studySchema);

        return $datasetSchema;
    }

    /**
     * Use Bioschemas Study type to represent an nmrXiv study without its relations.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $studySchema
     */
    public function studyLite($study)
    {
        $prefix = $study->project->name.':';
        $studySchema = Bioschemas::Study();
        $studySchema['@id'] = $study->doi;
        $studySchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://bioschemas.org/types/Study/0.3-DRAFT', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#study']);
        $studySchema->name($prefix.$study->name);
        $studySchema->description($study->description);
        $studySchema->keywords(BioschemasHelper::getTags($study));
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

    /**
     * Use Bioschemas Study type to represent an nmrXiv study with its relations.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $studySchema
     */
    public function study($study)
    {
        $studySchema = $this->studyLite($study);
        $studySchema->isPartOf($this->projectLite($study->project));
        $studySchema->hasPart($this->prepareDatasets($study));

        return $studySchema;
    }

    /**
     * Use Bioschemas Study type to represent an nmrXiv project without its relations.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Project  $project
     * @return object $projectSchema
     */
    public function projectLite($project)
    {
        $projectSchema = Bioschemas::Study();
        $projectSchema['@id'] = $project->doi;
        $projectSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://bioschemas.org/types/Study/0.3-DRAFT', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#investigation']);
        $projectSchema->name($project->name);
        $projectSchema->description($project->description);
        $projectSchema->keywords(BioschemasHelper::getTags($project));
        $projectSchema->license($project->license->url);
        $projectSchema->publisher(BioschemasHelper::preparePublisher());
        $projectSchema->url(env('APP_URL').'/'.explode(':', $project->identifier ? $project->identifier : ':')[1]);
        $projectSchema->dateCreated($project->created_at);
        $projectSchema->dateModified($project->updated_at);
        $projectSchema->datePublished($project->release_date);
        $projectSchema->author(BioschemasHelper::prepareAuthors($project));
        $projectSchema->citation(BioschemasHelper::prepareCitations($project));

        return $projectSchema;
    }

    /**
     * Use Bioschemas Study type to represent an nmrXiv project with its relations.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Project  $project
     * @return object $projectSchema
     */
    public function project($project)
    {
        $projectSchema = $this->projectLite($project);
        $projectSchema->hasPart($this->prepareStudies($project));

        return $projectSchema;
    }
}
