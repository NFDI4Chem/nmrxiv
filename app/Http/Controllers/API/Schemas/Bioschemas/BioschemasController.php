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
     * @param  App\Models\Study  $studyName  Optional
     * @param  App\Models\Dataset  $datasetName  Optional
     * @return object
     */
    /**
     * Bioschema
     *
     * @OA\Get (
     *     path="/api/v1/schemas/bioschemas/{username}/{project}",
     *     summary="Fetch bioschema for public model based on user id and slug",
     *     description="Fetch bioschema for public model based on user id and slug",
     *     operationId="bioschemaModelByName",
     *     tags={"schemas"},
     *
     * @OA\Parameter(
     *  name="username",
     *  in="path",
     *  description="nmrXiv username",
     *  required=true,
     *
     *      @OA\Schema(
     *          type="string",
     *    )
     * ),
     *
     * @OA\Parameter(
     *  name="project",
     *  in="path",
     *  description="nmrXiv project slug e.g. cheminfo-nmr-dataset-1",
     *  required=true,
     *
     *      @OA\Schema(
     *          type="string",
     *    )
     * ),
     *
     * @OA\Response(
     *    response=200,
     *    description="Success",
     * ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error"
     * )
     * )
     */
    // public function modelSchemaByName(Request $request, $username, $projectName, $studyName = null, $datasetName = null)
    // {
    //     $user = User::where('username', $username)->firstOrFail();
    //     if ($user) {
    //         $project = Project::where([['slug', $projectName], ['owner_id', $user->id]])->firstOrFail();
    //     }
    //     if ($project) {
    //         if ($project->is_public) {
    //             $projectSchema = $this->project($project);
    //             if ($studyName) {
    //                 $study = Study::where([['slug', $studyName], ['owner_id', $user->id], ['project_id', $project->id]])->firstOrFail();
    //                 if ($study) {
    //                     if ($study->is_public) {
    //                         $studySchema = $this->study($study);
    //                         if ($datasetName) {
    //                             $dataset = Dataset::where([['slug', $datasetName], ['owner_id', $user->id], ['project_id', $project->id], ['study_id', $study->id]])->firstOrFail();
    //                             if ($dataset) {
    //                                 if ($dataset->is_public) {
    //                                     $datasetSchema = $this->dataset($dataset);
    //                                 } else {
    //                                     throw new AuthorizationException;
    //                                 }

    //                                 return $datasetSchema;
    //                             }
    //                         }

    //                         return $studySchema;
    //                     } else {
    //                         throw new AuthorizationException;
    //                     }
    //                 }
    //             }

    //             return $projectSchema;
    //         } else {
    //             throw new AuthorizationException;
    //         }
    //     }
    // }

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
    /**
     * Bioschema
     *
     * @OA\Get (
     *     path="/api/v1/schemas/bioschemas/{id}",
     *     summary="Fetch bischema for model based on identifier",
     *     description="Fetch bischema for model based on identifier",
     *     operationId="bioschemaModel",
     *     tags={"schemas"},
     *
     * @OA\Parameter(
     *  name="id",
     *  in="path",
     *  description="Public model identifier for Project,Sample or Dataset e.g. P10 for Project,S70 for Sample or D399 for Dataset",
     *  required=true,
     *
     *      @OA\Schema(
     *          type="string",
     *    )
     * ),
     *
     * @OA\Response(
     *    response=200,
     *    description="Success",
     * ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error"
     * )
     * )
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
            $pubchemDataJSON = json_decode(Http::get($pubchemLink)->body(), true);
            $cid = '';
            $iupacName = '';
            if (array_key_exists('PropertyTable', $pubchemDataJSON)) {
                $cid = $pubchemDataJSON['PropertyTable']['Properties'][0]['CID'];
                $iupacName = $pubchemDataJSON['PropertyTable']['Properties'][0]['IUPACName'];
            }

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
        $sampleSchema->name($sample->name);
        $sampleSchema->description($sample->description);
        $sampleSchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $sampleSchema->hasBioChemEntityPart($this->prepareMoleculesSchemas($sample));

        return $sampleSchema;
    }

    /**
     * Represent the NMR experiment as a DefinedTerm
     *
     * @param  App\Models\Dataset  $dataset
     * @return array $array
     */
    public function prepareExperiment($dataset)
    {
        $info = Dataset::getNMRiumInfo($dataset);
        $experimentSchema = null;
        if ($info) {
            if (property_exists($info, 'nucleus') && property_exists($info, 'experiment')) {
                $chmo = BioschemasHelper::prepareDefinedTermSet('Chemical Methods Ontology', 'http://purl.obolibrary.org/obo/chmo.owl');

                $nucleus = $info->nucleus;
                $experiment = $info->experiment;
                $experimentSchema = $experiment;

                if ($experiment == '1d') {
                    if ($nucleus == '1H') {
                        $experiment = 'proton';
                    } elseif ($nucleus == '13C') {
                        $experiment = 'c13';
                    }
                }
                if ($experiment == 'proton') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('1H nuclear magnetic resonance spectroscopy', ['1H-NMR spectrometry', 'proton nuclear magnetic resonance spectroscopy', '1H-NMR spectroscopy', '1H-NMR', '1H NMR', '1H NMR spectroscopy', '1H nuclear magnetic resonance spectrometry', 'proton NMR'], 'CHMO:0000593', 'http://purl.obolibrary.org/obo/CHMO_0000593', $chmo);
                } elseif ($experiment == 'c13') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('13C nuclear magnetic resonance spectroscopy', ['13C-NMR spectrometry', '13C nuclear magnetic resonance spectrometry', '13C-NMR spectroscopy', 'carbon NMR', '13C NMR spectroscopy', '13C NMR', 'C-NMR'], 'CHMO:0000595', 'http://purl.obolibrary.org/obo/CHMO_0000595', $chmo);
                } elseif ($experiment == 'cosy') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('correlation spectroscopy', ['correlation spectrometry', 'correlated spectroscopy', 'correlated spectrometry', 'COSY'], 'CHMO:0000599', 'http://purl.obolibrary.org/obo/CHMO_0000599', $chmo);
                } elseif ($experiment == 'hmbc') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('heteronuclear multiple bond coherence', ['HMBC', 'HMBC NMR'], 'CHMO:0000601', 'http://purl.obolibrary.org/obo/CHMO_0000601', $chmo);
                } elseif ($experiment == 'hmqc') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('heteronuclear multiple quantum coherence', ['HMQC', 'HMQC NMR'], 'CHMO:0000603', 'http://purl.obolibrary.org/obo/CHMO_0000603', $chmo);
                } elseif ($experiment == 'hsqc') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('heteronuclear single quantum coherence', ['HSQC'], 'CHMO:0000604', 'http://purl.obolibrary.org/obo/CHMO_0000604', $chmo);
                } elseif ($experiment == 'tocsy') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('total correlation spectroscopy', ['homonuclear Hartmann-Hahn spectroscopy', 'homonuclear Hartmann Hahn spectroscopy', 'total correlation spectrometry', 'HOHAHA spectroscopy', 'TOCSY', 'total correlated spectroscopy', 'homonuclear Hartmann,Hahn spectroscopy', 'HOHAHA spectrometry'], 'CHMO:0000605', 'http://purl.obolibrary.org/obo/CHMO_0000605', $chmo);
                } elseif ($experiment == 'roesy') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('rotating frame Overhauser effect spectroscopy', ['rotating frame Overhauser enhancement spectroscopy', 'rotating frame Overhauser enhancement spectrometry', 'rOesy', 'cross-relaxation appropriate for minimolecules eμlated by locked spins', 'ROESY', 'CAMELPSIN', 'rotational Overhauser effect spectroscopy', 'rotating frame Overhauser effect spectrometry', 'ROESY NMR'], 'CHMO:0000610', 'http://purl.obolibrary.org/obo/CHMO_0000610', $chmo);
                } elseif ($experiment == 'dept') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('distortionless enhancement with polarization transfer', ['distortionless enhancement with polarisation transfer', 'distortionless enhancement by polarisation transfer', 'distortionless enhancement by polarization transfer', 'DEPT NMR', 'DEPT', 'distortionless enhancement with polarization transfer'], 'CHMO:0000596', 'http://purl.obolibrary.org/obo/CHMO_0000596', $chmo);
                }
            }
        }

        return $experimentSchema;
    }

    /**
     * Represent NMRium info as PropertyValue schemas.
     *
     * @param  App\Models\Dataset  $dataset
     * @return array $array
     */
    public function prepareNMRiumInfo($dataset)
    {
        $dict = Dataset::extractNMRiumInfo($dataset);
        if ($dict) {
            $solventProperty = BioschemasHelper::preparePropertyValue('NMR solvent', 'NMR:1000330', $dict['solvent'], null);
            $nucleusProperty = BioschemasHelper::preparePropertyValue('acquisition nucleus', 'NMR:1400083', $dict['nucleus'], null);
            $dimensionProperty = BioschemasHelper::preparePropertyValue('NMR spectrum by dimensionality', 'NMR:1000117', $dict['dimension'], null);
            $probeNameProperty = BioschemasHelper::preparePropertyValue('NMR probe', 'OBI:0000516', $dict['probeName'], null);
            //$experimentProperty = BioschemasHelper::preparePropertyValue('pulsed nuclear magnetic resonance spectroscopy', 'CHMO:0000613', $dict['experiment'], null);
            $temperatureProperty = BioschemasHelper::preparePropertyValue('Temperature', 'NCIT:C25206', $dict['temperature'], 'http://purl.obolibrary.org/obo/UO_0000012');
            $baseFrequencyProperty = BioschemasHelper::preparePropertyValue('irradiation frequency', 'NMR:1400026', $dict['baseFrequency'], 'http://purl.obolibrary.org/obo/UO_0000325');
            $fieldStrengthProperty = BioschemasHelper::preparePropertyValue('magnetic field strength', 'MR:1400253', $dict['fieldStrength'], 'http://purl.obolibrary.org/obo/UO_0000228');
            $numberOfScansProperty = BioschemasHelper::preparePropertyValue('number of scans', 'NMR:1400087', $dict['numberOfScans'], 'scans');
            $pulseSequenceProperty = BioschemasHelper::preparePropertyValue('nuclear magnetic resonance pulse sequence', 'CHMO:0001841', $dict['pulseSequence'], null);
            $spectralWidthProperty = BioschemasHelper::preparePropertyValue('Spectral Width', 'NCIT:C156496', $dict['spectralWidth'], 'http://purl.obolibrary.org/obo/UO_0000169');
            $numberOfPointsProperty = BioschemasHelper::preparePropertyValue('number of data points', 'NMR:1000176', $dict['numberOfPoints'], 'points');
            $relaxationTimeProperty = BioschemasHelper::preparePropertyValue('relaxation time measurement', 'FIX:0000202', $dict['relaxationTime'], 'http://purl.obolibrary.org/obo/UO_0000010');

            $keywords = [$dict['solvent'], $dict['dimension'].'D'];
            if ($dict['nucleus'] !== null) {
                foreach ($dict['nucleus'] as $keyword) {

                    array_push($keywords, $keyword);
                }
            }

            $variables = [$solventProperty, $nucleusProperty,  $dimensionProperty, $probeNameProperty,
                $temperatureProperty, $baseFrequencyProperty, $fieldStrengthProperty, $numberOfScansProperty, $pulseSequenceProperty, $spectralWidthProperty, $numberOfPointsProperty, $relaxationTimeProperty, ];

            $array = [$keywords, $variables, $dict['experiment']];

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
            $study = $dataset->study;
            $prefix = $dataset->study->name;

            $datasetSchema = Schema::Dataset();
            $datasetSchema['@id'] = $dataset->doi;
            $datasetSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://schema.org/Dataset', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#assay']);
            $datasetSchema->name($prefix.'['.$dataset->name.']');
            $datasetSchema->description($dataset->description);
            $datasetSchema->keywords($nmriumInfo[0]);
            $datasetSchema->license($dataset->study->license->url);
            $datasetSchema->url(env('APP_URL').'/'.explode(':', $dataset->identifier ? $dataset->identifier : ':')[1]);
            $datasetSchema->dateCreated($dataset->created_at);
            $datasetSchema->dateModified($dataset->updated_at);
            $datasetSchema->datePublished($dataset->release_date);
            $datasetSchema->distribution(BioschemasHelper::prepareDataDownload($dataset));
            $datasetSchema->includedInDataCatalog(BioschemasHelper::prepareDataCatalogLite());
            $datasetSchema->measurementTechnique($this->prepareExperiment($dataset));
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
        $datasetSchema = $this->datasetLite($dataset);
        $studySchema = $this->studyLite($dataset->study);

        if ($dataset->project) {
            $projectSchema = $this->projectLite($dataset->project);
            $studySchema->isPartOf($projectSchema);
        }
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

        $studySchema = Bioschemas::Study();
        $studySchema['@id'] = $study->doi;
        $studySchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://bioschemas.org/types/Study/0.3-DRAFT', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#study']);
        $studySchema->name($study->name);
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
        if (property_exists($study, 'project')) {
            $studySchema->isPartOf($this->projectLite($study->project));
        }
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
